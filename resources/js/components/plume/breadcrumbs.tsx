import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

import { cn } from '@/lib/utils';

interface BreadcrumbItem {
    label: string;
    href?: string;
}

interface BreadcrumbsProps {
    items: BreadcrumbItem[];
    className?: string;
}

export function Breadcrumbs({ items, className }: BreadcrumbsProps) {
    return (
        <nav className={cn('flex items-center gap-1.5 text-sm text-muted-foreground', className)} data-slot="breadcrumbs">
            {items.map((item, index) => (
                <span key={index} className="flex items-center gap-1.5">
                    {index > 0 && <ChevronRight className="size-3.5" />}
                    {item.href ? (
                        <Link href={item.href} className="hover:text-foreground">
                            {item.label}
                        </Link>
                    ) : (
                        <span className="text-foreground">{item.label}</span>
                    )}
                </span>
            ))}
        </nav>
    );
}
