import { usePlumeSearch } from '@/hooks/use-plume-search';
import { cn } from '@/lib/utils';
import type { SearchHit } from '@/types/search';
import { router } from '@inertiajs/react';
import { ChevronRight, FileText, Loader2, Search } from 'lucide-react';
import { Dialog as DialogPrimitive } from 'radix-ui';
import { type ReactNode, useCallback, useEffect, useMemo, useRef, useState } from 'react';

type SearchDialogProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    indexUrl: string | null | undefined;
};

const RESULT_LIMIT = 30;

function tokensFromQuery(query: string): string[] {
    return Array.from(
        new Set(
            query
                .toLowerCase()
                .split(/\s+/u)
                .map((token) => token.trim())
                .filter((token) => token.length > 1),
        ),
    );
}

function escapeRegex(value: string): string {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function HighlightedText({ text, tokens }: { text: string; tokens: string[] }): ReactNode {
    if (tokens.length === 0 || text === '') return text;

    const pattern = new RegExp(`(${tokens.map(escapeRegex).join('|')})`, 'giu');
    const parts = text.split(pattern);

    return parts.map((part, idx) =>
        pattern.test(part) ? (
            <mark key={idx} className="rounded bg-yellow-200/80 px-0.5 text-foreground dark:bg-yellow-400/30">
                {part}
            </mark>
        ) : (
            <span key={idx}>{part}</span>
        ),
    );
}

function buildSnippet(body: string, tokens: string[], maxLength = 220): string {
    if (body === '') return '';
    if (tokens.length === 0) return body.slice(0, maxLength) + (body.length > maxLength ? '…' : '');

    const lower = body.toLowerCase();
    let earliest = -1;

    for (const token of tokens) {
        const index = lower.indexOf(token);
        if (index !== -1 && (earliest === -1 || index < earliest)) {
            earliest = index;
        }
    }

    if (earliest === -1) return body.slice(0, maxLength) + (body.length > maxLength ? '…' : '');

    const before = Math.floor(maxLength * 0.4);
    let start = Math.max(0, earliest - before);
    let end = Math.min(body.length, start + maxLength);

    if (end - start < maxLength) {
        start = Math.max(0, end - maxLength);
    }

    while (start > 0 && body[start] !== ' ' && earliest - start > before - 20) {
        start += 1;
    }

    while (end < body.length && body[end] !== ' ' && end - earliest < maxLength - before + 20) {
        end -= 1;
        if (end <= earliest) break;
    }

    const prefix = start > 0 ? '… ' : '';
    const suffix = end < body.length ? ' …' : '';

    return prefix + body.slice(start, end).trim() + suffix;
}

function groupHits(hits: SearchHit[]): Array<{ label: string; hits: SearchHit[] }> {
    const groups = new Map<string, SearchHit[]>();

    for (const hit of hits) {
        const label = hit.group ?? 'Other';
        const existing = groups.get(label);
        if (existing) {
            existing.push(hit);
        } else {
            groups.set(label, [hit]);
        }
    }

    return Array.from(groups.entries()).map(([label, hits]) => ({ label, hits }));
}

export function SearchDialog({ open, onOpenChange, indexUrl }: SearchDialogProps) {
    const { status, search, listAll } = usePlumeSearch({ url: indexUrl, enabled: true });
    const [query, setQuery] = useState('');
    const [activeIndex, setActiveIndex] = useState(0);
    const inputRef = useRef<HTMLInputElement>(null);
    const resultsRef = useRef<HTMLDivElement>(null);

    const trimmedQuery = query.trim();
    const isBrowsing = open && trimmedQuery === '';
    const rawHits = useMemo(() => {
        if (!open) return [];
        if (trimmedQuery === '') return listAll();
        return search(trimmedQuery, RESULT_LIMIT);
    }, [open, trimmedQuery, search, listAll]);
    const tokens = useMemo(() => tokensFromQuery(query), [query]);
    const grouped = useMemo(() => groupHits(rawHits), [rawHits]);
    const hits = useMemo(() => grouped.flatMap((group) => group.hits), [grouped]);

    useEffect(() => {
        setActiveIndex(0);
    }, [query, hits.length]);

    useEffect(() => {
        if (open) {
            const timeout = window.setTimeout(() => inputRef.current?.focus(), 0);
            return () => window.clearTimeout(timeout);
        }

        setQuery('');
        setActiveIndex(0);

        return undefined;
    }, [open]);

    const handleNavigate = useCallback(
        (hit: SearchHit) => {
            onOpenChange(false);
            router.visit(hit.href);
        },
        [onOpenChange],
    );

    const onKeyDown = useCallback(
        (event: React.KeyboardEvent<HTMLInputElement>) => {
            if (hits.length === 0) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActiveIndex((current) => (current + 1) % hits.length);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActiveIndex((current) => (current - 1 + hits.length) % hits.length);
            } else if (event.key === 'Enter') {
                event.preventDefault();
                const hit = hits[activeIndex];
                if (hit) handleNavigate(hit);
            }
        },
        [hits, activeIndex, handleNavigate],
    );

    useEffect(() => {
        const node = resultsRef.current?.querySelector<HTMLElement>(`[data-result-index="${activeIndex}"]`);
        node?.scrollIntoView({ block: 'nearest' });
    }, [activeIndex]);

    return (
        <DialogPrimitive.Root open={open} onOpenChange={onOpenChange}>
            <DialogPrimitive.Portal>
                <DialogPrimitive.Overlay className="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0" />
                <DialogPrimitive.Content
                    aria-describedby={undefined}
                    className="fixed top-[10vh] left-1/2 z-50 flex max-h-[80vh] w-full max-w-2xl -translate-x-1/2 flex-col overflow-hidden rounded-xl border border-border bg-popover text-popover-foreground shadow-2xl data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[state=open]:animate-in data-[state=open]:fade-in-0 data-[state=open]:zoom-in-95"
                >
                    <DialogPrimitive.Title className="sr-only">Search documentation</DialogPrimitive.Title>

                    <div className="flex items-center gap-3 border-b border-border px-4 py-3">
                        <Search className="size-4 shrink-0 text-muted-foreground" />
                        <input
                            ref={inputRef}
                            value={query}
                            onChange={(event) => setQuery(event.target.value)}
                            onKeyDown={onKeyDown}
                            placeholder="Search documentation…"
                            className="flex-1 bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                            autoComplete="off"
                            spellCheck={false}
                        />
                        {status === 'loading' && <Loader2 className="size-4 shrink-0 animate-spin text-muted-foreground" />}
                        <kbd className="rounded border border-border bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground">ESC</kbd>
                    </div>

                    <div ref={resultsRef} className="min-h-0 flex-1 overflow-y-auto">
                        {status === 'error' && (
                            <div className="px-4 py-8 text-center text-sm text-muted-foreground">
                                Could not load the search index. Try refreshing the page.
                            </div>
                        )}

                        {status !== 'error' && status !== 'ready' && isBrowsing && (
                            <div className="px-4 py-8 text-center text-sm text-muted-foreground">Preparing the search index…</div>
                        )}

                        {status !== 'error' && !isBrowsing && hits.length === 0 && (
                            <div className="px-4 py-8 text-center text-sm text-muted-foreground">
                                No results for <span className="font-medium text-foreground">{query}</span>
                            </div>
                        )}

                        {grouped.length > 0 && (
                            <div className="py-2">
                                {grouped.map((group) => (
                                    <div key={group.label}>
                                        <div className="px-4 pt-3 pb-1 text-[10px] font-semibold tracking-wider text-muted-foreground uppercase">
                                            {group.label}
                                        </div>
                                        <ul className="flex flex-col">
                                            {group.hits.map((hit) => {
                                                const indexInList = hits.indexOf(hit);
                                                const isActive = indexInList === activeIndex;

                                                return (
                                                    <li key={hit.id}>
                                                        <button
                                                            type="button"
                                                            data-result-index={indexInList}
                                                            onMouseEnter={() => setActiveIndex(indexInList)}
                                                            onClick={() => handleNavigate(hit)}
                                                            className={cn(
                                                                'group flex w-full items-start gap-3 px-4 py-2.5 text-left text-sm transition-colors',
                                                                isActive ? 'bg-accent text-accent-foreground' : 'hover:bg-accent/50',
                                                            )}
                                                        >
                                                            <FileText className="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                                                            <div className="min-w-0 flex-1">
                                                                <div className="truncate font-medium">
                                                                    <HighlightedText text={hit.title} tokens={tokens} />
                                                                </div>
                                                                {isBrowsing
                                                                    ? hit.description && (
                                                                          <div className="mt-0.5 line-clamp-2 text-xs text-muted-foreground">
                                                                              {hit.description}
                                                                          </div>
                                                                      )
                                                                    : hit.body && (
                                                                          <div className="mt-0.5 line-clamp-2 text-xs leading-relaxed text-muted-foreground">
                                                                              <HighlightedText
                                                                                  text={buildSnippet(hit.body, tokens)}
                                                                                  tokens={tokens}
                                                                              />
                                                                          </div>
                                                                      )}
                                                            </div>
                                                            <ChevronRight
                                                                className={cn(
                                                                    'mt-0.5 size-4 shrink-0 text-muted-foreground transition-opacity',
                                                                    isActive ? 'opacity-100' : 'opacity-0 group-hover:opacity-100',
                                                                )}
                                                            />
                                                        </button>
                                                    </li>
                                                );
                                            })}
                                        </ul>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="hidden items-center gap-4 border-t border-border bg-muted/30 px-4 py-2 text-[11px] text-muted-foreground sm:flex">
                        <span className="flex items-center gap-1">
                            <kbd className="rounded border border-border bg-background px-1 py-0.5">↑</kbd>
                            <kbd className="rounded border border-border bg-background px-1 py-0.5">↓</kbd>
                            to navigate
                        </span>
                        <span className="flex items-center gap-1">
                            <kbd className="rounded border border-border bg-background px-1 py-0.5">⏎</kbd>
                            to open
                        </span>
                        <span className="flex items-center gap-1">
                            <kbd className="rounded border border-border bg-background px-1 py-0.5">ESC</kbd>
                            to close
                        </span>
                    </div>
                </DialogPrimitive.Content>
            </DialogPrimitive.Portal>
        </DialogPrimitive.Root>
    );
}
