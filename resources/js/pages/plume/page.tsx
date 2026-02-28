import { type SectionProps, Section } from '@/components/blocks/section';
import { type DocsFooterProps, DocsFooter } from '@/components/plume/docs-footer';
import { type DocsHeaderProps, DocsHeader } from '@/components/plume/docs-header';
import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { type CollectionInfo, type NavItem, SidebarNav } from '@/components/plume/sidebar-nav';
import { TableOfContents } from '@/components/plume/table-of-contents';
import { Head } from '@inertiajs/react';

interface CollectionPageProps extends PlumePageContext {
    navigation?: NavItem[];
    collection?: CollectionInfo;
    sections?: SectionProps[];
    header?: DocsHeaderProps;
    footer?: DocsFooterProps;
}

export default function Page(page: CollectionPageProps) {
    const hasNav = page.navigation && page.navigation.length > 0;
    const hasSections = page.sections && page.sections.length > 0;

    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            <DocsHeader {...page.header} collectionTitle={page.collection?.title} />
            {hasNav ? (
                <div className="mx-auto flex max-w-368 gap-8 px-6 lg:px-8">
                    <SidebarNav navigation={page.navigation!} collection={page.collection} />
                    <main className="min-w-0 flex-1">
                        {hasSections && page.sections!.map((section, index) => <Section key={index} {...section} />)}
                        <article className="mx-auto prose max-w-full pt-8 pb-14 lg:max-w-3xl dark:prose-invert">
                            <MarkdownRenderer page={page} />
                        </article>
                        <DocsFooter {...page.footer} />
                    </main>
                    <div className="sticky top-[calc(var(--spacing)*16+1px)] h-[calc(100vh-var(--spacing)*16-5px)] w-64 shrink-0 overflow-y-auto pb-8 text-sm max-xl:hidden">
                        <TableOfContents />
                    </div>
                </div>
            ) : (
                <article className="mx-auto prose max-w-full px-6 py-12 lg:max-w-3xl dark:prose-invert">
                    <MarkdownRenderer page={page} />
                </article>
            )}
        </>
    );
}
