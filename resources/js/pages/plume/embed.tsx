import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { useDarkMode } from '@/hooks/use-dark-mode';
import { type EmbedScale, embedScale, followLinksFromFrame, tellHost } from '@/lib/embed';
import { cn } from '@/lib/utils';
import { Head, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

// Written out in full, so that Tailwind finds each class in the source.
const PROSE_SIZES: Record<EmbedScale, string> = {
    sm: 'prose-sm',
    base: 'prose-base',
    lg: 'prose-lg',
};

interface EmbeddedPageProps extends PlumePageContext {
    vault?: { prefix: string };
}

/** Any page opened with `?embed`, whatever its layout: its content alone. */
export default function EmbeddedPage(page: EmbeddedPageProps) {
    const { url } = usePage();
    const prefix = page.vault?.prefix ?? '/';
    const title = page.title ?? null;

    // The header applies the light or dark choice on full pages; there is none here.
    useDarkMode();

    useEffect(() => followLinksFromFrame(prefix), [prefix]);

    useEffect(() => tellHost(title), [url, title]);

    return (
        <>
            <Head>{title && <title>{title}</title>}</Head>
            {/* No sticky header above the headings: an anchor lands at the top of the frame. */}
            <article className={cn('prose max-w-none px-6 py-6 dark:prose-invert [&_:is(h2,h3,h4)]:scroll-mt-6', PROSE_SIZES[embedScale])}>
                <MarkdownRenderer page={page} />
            </article>
        </>
    );
}
