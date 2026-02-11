import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { useState } from 'react';

import { cn } from '@/lib/utils';
import type { SidebarSection } from '@/types/plume';

interface SidebarProps {
    sections: SidebarSection[];
    prefix: string;
    currentSlug: string;
    className?: string;
}

export function Sidebar({ sections, prefix, currentSlug, className }: SidebarProps) {
    return (
        <nav className={cn('space-y-6', className)} data-slot="sidebar">
            {sections.map((section) => (
                <SidebarSectionGroup key={section.title} section={section} prefix={prefix} currentSlug={currentSlug} />
            ))}
        </nav>
    );
}

function SidebarSectionGroup({ section, prefix, currentSlug }: { section: SidebarSection; prefix: string; currentSlug: string }) {
    const [isOpen, setIsOpen] = useState(true);

    return (
        <div data-slot="sidebar-section">
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="flex w-full items-center gap-1 text-sm font-semibold text-foreground hover:text-foreground/80"
            >
                <ChevronRight className={cn('size-3.5 shrink-0 transition-transform', isOpen && 'rotate-90')} />
                {section.title}
            </button>
            {isOpen && (
                <ul className="mt-1.5 space-y-0.5 border-l border-border pl-3">
                    {section.pages.map((page) => (
                        <li key={page.slug}>
                            <Link
                                href={`${prefix}/${page.slug}`}
                                className={cn(
                                    'block rounded-md px-2 py-1 text-sm transition-colors',
                                    page.slug === currentSlug
                                        ? 'bg-accent font-medium text-accent-foreground'
                                        : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground',
                                )}
                            >
                                {page.title}
                            </Link>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
