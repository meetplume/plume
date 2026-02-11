import { Menu, X } from 'lucide-react';
import { useState } from 'react';

import { cn } from '@/lib/utils';
import type { Page, SidebarSection, TocItem } from '@/types/plume';

import { Breadcrumbs } from './breadcrumbs';
import { DocHeader } from './doc-header';
import { MarkdownRenderer } from './markdown-renderer';
import { PageNavigation } from './page-navigation';
import { SearchDialog } from './search-dialog';
import { Sidebar } from './sidebar';
import { TableOfContents } from './table-of-contents';

interface DocumentationLayoutProps {
    collectionTitle: string;
    prefix: string;
    sections: SidebarSection[];
    allPages: Page[];
    currentPage: Page;
    currentSlug: string;
    previousPage: Page | null;
    nextPage: Page | null;
    tocItems: TocItem[];
}

export function DocumentationLayout({
    collectionTitle,
    prefix,
    sections,
    allPages,
    currentPage,
    currentSlug,
    previousPage,
    nextPage,
    tocItems,
}: DocumentationLayoutProps) {
    const [searchOpen, setSearchOpen] = useState(false);
    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <div className="flex min-h-screen flex-col bg-background" data-slot="documentation-layout">
            <DocHeader title={collectionTitle} onSearchOpen={() => setSearchOpen(true)} />

            <div className="flex flex-1">
                {/* Mobile sidebar toggle */}
                <button
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    className="fixed right-4 bottom-4 z-40 flex size-10 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-lg lg:hidden"
                >
                    {sidebarOpen ? <X className="size-5" /> : <Menu className="size-5" />}
                </button>

                {/* Sidebar */}
                <aside
                    className={cn(
                        'fixed inset-y-0 top-[49px] left-0 z-30 w-64 shrink-0 overflow-y-auto border-r border-border bg-background p-4 transition-transform lg:sticky lg:translate-x-0',
                        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                    )}
                >
                    <Sidebar sections={sections} prefix={prefix} currentSlug={currentSlug} />
                </aside>

                {/* Mobile sidebar overlay */}
                {sidebarOpen && (
                    <div className="fixed inset-0 z-20 bg-background/80 backdrop-blur-sm lg:hidden" onClick={() => setSidebarOpen(false)} />
                )}

                {/* Main content */}
                <main className="min-w-0 flex-1 px-6 py-8 lg:px-10">
                    <div className="mx-auto max-w-3xl">
                        <Breadcrumbs items={[{ label: collectionTitle, href: prefix }, { label: currentPage.title }]} className="mb-6" />
                        <article className="prose max-w-none dark:prose-invert">
                            <MarkdownRenderer content={currentPage.rawContent} />
                        </article>
                        <PageNavigation previous={previousPage} next={nextPage} prefix={prefix} className="mt-10" />
                    </div>
                </main>

                {/* Table of Contents */}
                <aside className="sticky top-[49px] hidden h-[calc(100vh-49px)] w-56 shrink-0 overflow-y-auto p-4 xl:block">
                    <TableOfContents items={tocItems} />
                </aside>
            </div>

            <SearchDialog pages={allPages} prefix={prefix} open={searchOpen} onOpenChange={setSearchOpen} />
        </div>
    );
}
