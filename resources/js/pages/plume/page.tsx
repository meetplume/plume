import { Head } from '@inertiajs/react';
import { MarkdownRenderer } from '../../components/plume/markdown-renderer';

interface PageProps {
    content: string;
    title?: string;
    description?: string;
    meta?: Record<string, unknown>;
    codeThemeLight?: string;
    codeThemeDark?: string;
}

export default function Page({ content, title, description, codeThemeLight, codeThemeDark }: PageProps) {
    return (
        <>
            <Head>
                {title && <title>{title}</title>}
                {description && <meta name="description" content={description} />}
            </Head>
            <article className="mx-auto prose max-w-3xl px-6 py-12 dark:prose-invert">
                <MarkdownRenderer content={content} codeThemeLight={codeThemeLight} codeThemeDark={codeThemeDark} />
            </article>
        </>
    );
}
