import type { SearchHit, SearchRecord, SearchStatus } from '@/types/search';
import MiniSearch from 'minisearch';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

type UsePlumeSearchOptions = {
    url: string | null | undefined;
    enabled?: boolean;
};

type UsePlumeSearchResult = {
    status: SearchStatus;
    search: (query: string, limit?: number) => SearchHit[];
    listAll: () => SearchHit[];
};

const SESSION_CACHE_PREFIX = 'plume:search-index:';

type CachedPayload = {
    etag: string | null;
    records: SearchRecord[];
};

function readSessionCache(url: string): CachedPayload | null {
    try {
        const raw = sessionStorage.getItem(SESSION_CACHE_PREFIX + url);
        if (!raw) return null;
        const parsed = JSON.parse(raw) as CachedPayload;
        if (!Array.isArray(parsed.records)) return null;
        return parsed;
    } catch {
        return null;
    }
}

function writeSessionCache(url: string, payload: CachedPayload): void {
    try {
        sessionStorage.setItem(SESSION_CACHE_PREFIX + url, JSON.stringify(payload));
    } catch {
        // sessionStorage may be full or disabled; non-fatal
    }
}

function scheduleIdle(callback: () => void): () => void {
    type IdleWindow = Window & {
        requestIdleCallback?: (cb: () => void, opts?: { timeout: number }) => number;
        cancelIdleCallback?: (handle: number) => void;
    };

    const w = window as IdleWindow;

    if (typeof w.requestIdleCallback === 'function') {
        const handle = w.requestIdleCallback(callback, { timeout: 2000 });
        return () => w.cancelIdleCallback?.(handle);
    }

    const handle = window.setTimeout(callback, 200);
    return () => window.clearTimeout(handle);
}

function buildIndex(records: SearchRecord[]): MiniSearch<SearchRecord> {
    const index = new MiniSearch<SearchRecord>({
        idField: 'id',
        fields: ['title', 'headings', 'description', 'body'],
        storeFields: ['slug', 'title', 'description', 'headings', 'body', 'href', 'group'],
        extractField: (record, field) => {
            const value = record[field as keyof SearchRecord];
            if (Array.isArray(value)) return value.join(' ');
            return value == null ? '' : String(value);
        },
        searchOptions: {
            boost: { title: 5, headings: 3, description: 2 },
            prefix: true,
            fuzzy: 0.2,
            combineWith: 'AND',
        },
    });

    index.addAll(records);

    return index;
}

export function usePlumeSearch({ url, enabled = true }: UsePlumeSearchOptions): UsePlumeSearchResult {
    const [status, setStatus] = useState<SearchStatus>('idle');
    const recordsRef = useRef<SearchRecord[] | null>(null);
    const indexRef = useRef<MiniSearch<SearchRecord> | null>(null);
    const abortRef = useRef<AbortController | null>(null);

    useEffect(() => {
        if (!enabled || !url) return undefined;

        recordsRef.current = null;
        indexRef.current = null;
        setStatus('idle');

        const cached = readSessionCache(url);

        if (cached) {
            recordsRef.current = cached.records;
            setStatus('ready');
            return undefined;
        }

        const cancelIdle = scheduleIdle(() => {
            const controller = new AbortController();
            abortRef.current = controller;
            setStatus('loading');

            fetch(url, { signal: controller.signal, credentials: 'same-origin' })
                .then(async (response) => {
                    if (!response.ok) throw new Error(`Search index responded with ${response.status}`);
                    const etag = response.headers.get('ETag');
                    const records = (await response.json()) as SearchRecord[];
                    writeSessionCache(url, { etag, records });
                    recordsRef.current = records;
                    setStatus('ready');
                })
                .catch((error: unknown) => {
                    if (error instanceof DOMException && error.name === 'AbortError') return;
                    setStatus('error');
                });
        });

        return () => {
            cancelIdle();
            abortRef.current?.abort();
        };
    }, [url, enabled]);

    const ensureIndex = useCallback((): MiniSearch<SearchRecord> | null => {
        if (indexRef.current) return indexRef.current;
        const records = recordsRef.current;
        if (!records) return null;
        indexRef.current = buildIndex(records);
        return indexRef.current;
    }, []);

    const listAll = useCallback((): SearchHit[] => {
        const records = recordsRef.current;
        if (!records) return [];

        return records.map((record) => ({
            id: record.id,
            slug: record.slug,
            title: record.title,
            description: record.description,
            headings: record.headings,
            body: record.body,
            href: record.href,
            group: record.group,
            score: 0,
            matchedTerms: [],
        }));
    }, []);

    const search = useCallback(
        (query: string, limit = 20): SearchHit[] => {
            const trimmed = query.trim();
            if (trimmed === '') return [];

            const index = ensureIndex();
            if (!index) return [];

            const results = index.search(trimmed) as unknown as Array<{
                id: string;
                slug: string;
                title: string;
                description: string;
                headings: string[];
                body: string;
                href: string;
                group: string | null;
                score: number;
                terms: string[];
            }>;

            return results.slice(0, limit).map((result) => ({
                id: result.id,
                slug: result.slug,
                title: result.title,
                description: result.description,
                headings: result.headings,
                body: result.body,
                href: result.href,
                group: result.group,
                score: result.score,
                matchedTerms: result.terms,
            }));
        },
        [ensureIndex],
    );

    return useMemo(() => ({ status, search, listAll }), [status, search, listAll]);
}
