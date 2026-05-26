import { cn } from '@/lib/utils';
import { Search } from 'lucide-react';

type SearchButtonProps = {
    onClick: () => void;
    shortcut?: string;
    className?: string;
    variant?: 'desktop' | 'icon';
};

export function SearchButton({ onClick, shortcut, className, variant = 'desktop' }: SearchButtonProps) {
    if (variant === 'icon') {
        return (
            <button
                type="button"
                onClick={onClick}
                aria-label="Search documentation"
                className={cn('text-muted-foreground hover:text-foreground', className)}
            >
                <Search className="size-5" />
            </button>
        );
    }

    return (
        <button
            type="button"
            onClick={onClick}
            className={cn(
                'inline-flex w-56 items-center gap-2 rounded-md border border-border bg-background px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:border-foreground/20 hover:text-foreground',
                className,
            )}
        >
            <Search className="size-4 shrink-0" />
            <span>Search…</span>
            {shortcut && (
                <kbd className="ml-auto hidden rounded border border-border bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground sm:inline-block">
                    {shortcut}
                </kbd>
            )}
        </button>
    );
}
