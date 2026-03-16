import { type DocsFooterProps, DocsFooter } from '@/components/plume/docs-footer';
import { type DocsHeaderProps, DocsHeader } from '@/components/plume/docs-header';
import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { Head } from '@inertiajs/react';

interface ChangelogPageProps extends PlumePageContext {
    layout: 'changelog';
    header?: DocsHeaderProps;
    footer?: DocsFooterProps;
    site?: { name: string; logo: string | null; logoDark: string | null };
}

export default function ChangelogLayout(page: ChangelogPageProps) {
    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            <DocsHeader {...page.header} collectionTitle={page.site?.name} />
            <article className="mx-auto prose max-w-full px-6 py-12 lg:max-w-3xl dark:prose-invert">
                {page.title && <h1>{page.title}</h1>}
                {page.meta?.date && <time className="text-sm text-muted-foreground">{String(page.meta.date)}</time>}
                <MarkdownRenderer page={page} />
            </article>
            <DocsFooter {...page.footer} />
        </>
    );
}
