import { Head } from '@inertiajs/react';
import { type PlumePageContext, MarkdownRenderer } from '../../components/plume/markdown-renderer';

export default function Page(page: PlumePageContext) {
    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            <article className="mx-auto prose max-w-3xl px-6 py-12 dark:prose-invert">
                <MarkdownRenderer page={page} />
            </article>
        </>
    );
}
