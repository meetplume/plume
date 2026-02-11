import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

import { cn } from '@/lib/utils';
import type { Page } from '@/types/plume';

interface PageNavigationProps {
    previous: Page | null;
    next: Page | null;
    prefix: string;
    className?: string;
}

export function PageNavigation({ previous, next, prefix, className }: PageNavigationProps) {
    if (!previous && !next) {
        return null;
    }

    return (
        <nav className={cn('flex items-center justify-between gap-4 border-t border-border pt-6', className)} data-slot="page-navigation">
            <div>
                {previous && (
                    <Link
                        href={`${prefix}/${previous.slug}`}
                        className="group flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <ChevronLeft className="size-4" />
                        <div>
                            <div className="text-xs text-muted-foreground">Previous</div>
                            <div className="font-medium text-foreground group-hover:text-foreground">{previous.title}</div>
                        </div>
                    </Link>
                )}
            </div>
            <div>
                {next && (
                    <Link
                        href={`${prefix}/${next.slug}`}
                        className="group flex items-center gap-1 text-right text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <div>
                            <div className="text-xs text-muted-foreground">Next</div>
                            <div className="font-medium text-foreground group-hover:text-foreground">{next.title}</div>
                        </div>
                        <ChevronRight className="size-4" />
                    </Link>
                )}
            </div>
        </nav>
    );
}
