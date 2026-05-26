import { type SectionProps, Section } from '@/components/blocks/section';
import { type DocsFooterProps, DocsFooter } from '@/components/plume/docs-footer';
import { type DocsHeaderProps, DocsHeader } from '@/components/plume/docs-header';
import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { Head } from '@inertiajs/react';

interface PageLayoutProps extends PlumePageContext {
    layout: 'page';
    sections?: SectionProps[];
    header?: DocsHeaderProps;
    footer?: DocsFooterProps;
    site?: { name: string; logo: string | null; logoDark: string | null };
    searchIndexUrl?: string | null;
}

export default function PageLayout(page: PageLayoutProps) {
    const hasSections = page.sections && page.sections.length > 0;

    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            <DocsHeader {...page.header} collectionTitle={page.site?.name} searchIndexUrl={page.searchIndexUrl} />
            {hasSections && page.sections!.map((section, index) => <Section key={index} {...section} />)}
            <article className="mx-auto prose max-w-full px-6 py-12 lg:max-w-3xl dark:prose-invert">
                <MarkdownRenderer page={page} />
            </article>
            <DocsFooter {...page.footer} />
        </>
    );
}
