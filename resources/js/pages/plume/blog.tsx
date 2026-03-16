import { type DocsFooterProps, DocsFooter } from '@/components/plume/docs-footer';
import { type DocsHeaderProps, DocsHeader } from '@/components/plume/docs-header';
import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { Head } from '@inertiajs/react';

interface BlogPageProps extends PlumePageContext {
    layout: 'blog';
    header?: DocsHeaderProps;
    footer?: DocsFooterProps;
    site?: { name: string; logo: string | null; logoDark: string | null };
}

export default function BlogLayout(page: BlogPageProps) {
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
                {page.meta?.author && <p className="text-sm text-muted-foreground">By {String(page.meta.author)}</p>}
                <MarkdownRenderer page={page} />
            </article>
            <DocsFooter {...page.footer} />
        </>
    );
}
