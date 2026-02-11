import { Search } from 'lucide-react';

import { cn } from '@/lib/utils';

interface DocHeaderProps {
    title: string;
    onSearchOpen: () => void;
    className?: string;
}

export function DocHeader({ title, onSearchOpen, className }: DocHeaderProps) {
    return (
        <header className={cn('flex items-center justify-between border-b border-border px-6 py-3', className)} data-slot="doc-header">
            <h2 className="text-sm font-semibold text-foreground">{title}</h2>
            <button
                onClick={onSearchOpen}
                className="flex items-center gap-2 rounded-md border border-border bg-background px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
            >
                <Search className="size-3.5" />
                <span>Search...</span>
                <kbd className="pointer-events-none ml-2 hidden rounded border border-border bg-muted px-1.5 py-0.5 text-[10px] font-medium sm:inline-block">
                    <span className="text-xs">&#8984;</span>K
                </kbd>
            </button>
        </header>
    );
}
