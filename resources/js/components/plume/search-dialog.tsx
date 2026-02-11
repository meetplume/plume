import { router } from '@inertiajs/react';
import { FileText, Search } from 'lucide-react';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

import { cn } from '@/lib/utils';
import type { Page } from '@/types/plume';

interface SearchDialogProps {
    pages: Page[];
    prefix: string;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export function SearchDialog({ pages, prefix, open, onOpenChange }: SearchDialogProps) {
    const [query, setQuery] = useState('');
    const [selectedIndex, setSelectedIndex] = useState(0);
    const inputRef = useRef<HTMLInputElement>(null);

    const results = useMemo(() => {
        if (!query.trim()) {
            return pages;
        }

        const lowerQuery = query.toLowerCase();
        return pages.filter((page) => page.title.toLowerCase().includes(lowerQuery) || page.description.toLowerCase().includes(lowerQuery));
    }, [query, pages]);

    useEffect(() => {
        setSelectedIndex(0);
    }, [results]);

    useEffect(() => {
        if (open) {
            setQuery('');
            setTimeout(() => inputRef.current?.focus(), 0);
        }
    }, [open]);

    useEffect(() => {
        const handleKeyDown = (e: KeyboardEvent) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                onOpenChange(!open);
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [open, onOpenChange]);

    const navigate = useCallback(
        (slug: string) => {
            onOpenChange(false);
            router.visit(`${prefix}/${slug}`);
        },
        [prefix, onOpenChange],
    );

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setSelectedIndex((i) => Math.min(i + 1, results.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setSelectedIndex((i) => Math.max(i - 1, 0));
        } else if (e.key === 'Enter' && results[selectedIndex]) {
            navigate(results[selectedIndex].slug);
        } else if (e.key === 'Escape') {
            onOpenChange(false);
        }
    };

    if (!open) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-50 flex items-start justify-center pt-[20vh]" data-slot="search-dialog">
            <div className="fixed inset-0 bg-background/80 backdrop-blur-sm" onClick={() => onOpenChange(false)} />
            <div className="relative z-50 w-full max-w-lg overflow-hidden rounded-xl border border-border bg-popover shadow-lg">
                <div className="flex items-center gap-2 border-b border-border px-3">
                    <Search className="size-4 shrink-0 text-muted-foreground" />
                    <input
                        ref={inputRef}
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        onKeyDown={handleKeyDown}
                        placeholder="Search documentation..."
                        className="flex-1 bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground"
                    />
                </div>
                <div className="max-h-80 overflow-y-auto p-2">
                    {results.length === 0 ? (
                        <p className="py-6 text-center text-sm text-muted-foreground">No results found.</p>
                    ) : (
                        results.map((page, index) => (
                            <button
                                key={page.slug}
                                onClick={() => navigate(page.slug)}
                                className={cn(
                                    'flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm transition-colors',
                                    index === selectedIndex ? 'bg-accent text-accent-foreground' : 'text-foreground hover:bg-accent/50',
                                )}
                            >
                                <FileText className="size-4 shrink-0 text-muted-foreground" />
                                <div className="min-w-0">
                                    <div className="truncate font-medium">{page.title}</div>
                                    {page.description && <div className="truncate text-xs text-muted-foreground">{page.description}</div>}
                                </div>
                            </button>
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
