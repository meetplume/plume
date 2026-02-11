import { useEffect, useState } from 'react';

import { cn } from '@/lib/utils';
import type { TocItem } from '@/types/plume';

interface TableOfContentsProps {
    items: TocItem[];
    className?: string;
}

export function TableOfContents({ items, className }: TableOfContentsProps) {
    const [activeId, setActiveId] = useState<string>('');

    useEffect(() => {
        if (items.length === 0) {
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        setActiveId(entry.target.id);
                    }
                }
            },
            { rootMargin: '0px 0px -80% 0px', threshold: 0 },
        );

        for (const item of items) {
            const element = document.getElementById(item.id);
            if (element) {
                observer.observe(element);
            }
        }

        return () => observer.disconnect();
    }, [items]);

    if (items.length === 0) {
        return null;
    }

    return (
        <nav className={cn('space-y-1', className)} data-slot="toc">
            <p className="mb-2 text-sm font-semibold text-foreground">On this page</p>
            {items.map((item) => (
                <a
                    key={item.id}
                    href={`#${item.id}`}
                    className={cn(
                        'block text-sm transition-colors',
                        item.level === 3 && 'pl-3',
                        item.level === 4 && 'pl-6',
                        activeId === item.id ? 'font-medium text-foreground' : 'text-muted-foreground hover:text-foreground',
                    )}
                >
                    {item.text}
                </a>
            ))}
        </nav>
    );
}

export function extractTocItems(content: string): TocItem[] {
    const headingRegex = /^(#{2,4})\s+(.+)$/gm;
    const items: TocItem[] = [];
    let match;

    while ((match = headingRegex.exec(content)) !== null) {
        const level = match[1].length;
        const text = match[2].trim();
        const id = text
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-');

        items.push({ id, text, level });
    }

    return items;
}
