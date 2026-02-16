import { useEffect, useMemo, useState } from 'react';
import rehypeExpressiveCode, { type ThemeObjectOrShikiThemeName } from 'rehype-expressive-code';
import rehypeExternalLinks from 'rehype-external-links';
import rehypeRaw from 'rehype-raw';
import rehypeSlug from 'rehype-slug';
import rehypeStringify from 'rehype-stringify';
import remarkFrontmatter from 'remark-frontmatter';
import remarkGfm from 'remark-gfm';
import remarkParse from 'remark-parse';
import remarkRehype from 'remark-rehype';
import { unified } from 'unified';

export interface PlumePageContext {
    content: string;
    title?: string;
    description?: string;
    meta?: Record<string, unknown>;
    codeThemeLight?: string;
    codeThemeDark?: string;
}

interface MarkdownRendererProps {
    page: PlumePageContext;
    className?: string;
}

export function MarkdownRenderer({ page, className }: MarkdownRendererProps) {
    const [html, setHtml] = useState('');

    const processor = useMemo(
        () =>
            unified()
                .use(remarkParse)
                .use(remarkGfm)
                .use(remarkFrontmatter)
                .use(remarkRehype, { allowDangerousHtml: true })
                .use(rehypeRaw)
                .use(rehypeExpressiveCode, {
                    themes: [
                        (page.codeThemeDark ?? 'github-dark') as ThemeObjectOrShikiThemeName,
                        (page.codeThemeLight ?? 'github-light') as ThemeObjectOrShikiThemeName,
                    ],
                })
                .use(rehypeSlug)
                .use(rehypeExternalLinks, { target: '_blank', rel: ['noopener', 'noreferrer'] })
                .use(rehypeStringify),
        [page.codeThemeLight, page.codeThemeDark],
    );

    useEffect(() => {
        processor.process(page.content).then((file) => {
            setHtml(String(file));
        });
    }, [processor, page.content]);

    return <div className={className} dangerouslySetInnerHTML={{ __html: html }} />;
}
