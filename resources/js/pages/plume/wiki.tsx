import { type DocsFooterProps, DocsFooter } from '@/components/plume/docs-footer';
import { type DocsHeaderProps, DocsHeader } from '@/components/plume/docs-header';
import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { type NavItem, SidebarNav } from '@/components/plume/sidebar-nav';
import { TableOfContents } from '@/components/plume/table-of-contents';
import { Head } from '@inertiajs/react';

interface WikiPageProps extends PlumePageContext {
    layout: 'wiki';
    navigation: NavItem[];
    header?: DocsHeaderProps;
    footer?: DocsFooterProps;
    site?: { name: string; logo: string | null; logoDark: string | null };
}

export default function WikiLayout(page: WikiPageProps) {
    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            <DocsHeader {...page.header} collectionTitle={page.site?.name} />
            <div className="mx-auto flex max-w-368 gap-8 px-6 lg:px-8">
                <SidebarNav navigation={page.navigation} />
                <main className="min-w-0 flex-1">
                    <article className="mx-auto prose max-w-full pt-8 pb-14 lg:max-w-3xl dark:prose-invert">
                        <MarkdownRenderer page={page} />
                    </article>
                    <DocsFooter {...page.footer} />
                </main>
                <div className="sticky top-[calc(var(--spacing)*16+1px)] h-[calc(100vh-var(--spacing)*16-5px)] w-64 shrink-0 overflow-y-auto pb-8 text-sm max-xl:hidden">
                    <TableOfContents />
                </div>
            </div>
        </>
    );
}
