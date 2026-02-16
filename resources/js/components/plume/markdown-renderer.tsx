import { useEffect, useState } from 'react';
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
}

interface MarkdownRendererProps {
    page: PlumePageContext;
    className?: string;
}

const processor = unified()
    .use(remarkParse)
    .use(remarkGfm)
    .use(remarkFrontmatter)
    .use(remarkRehype, { allowDangerousHtml: true })
    .use(rehypeRaw)
    .use(rehypeSlug)
    .use(rehypeExternalLinks, { target: '_blank', rel: ['noopener', 'noreferrer'] })
    .use(rehypeStringify);

export function MarkdownRenderer({ page, className }: MarkdownRendererProps) {
    const [html, setHtml] = useState('');

    useEffect(() => {
        processor.process(page.content).then((file) => {
            setHtml(String(file));
        });
    }, [page.content]);

    return <div className={className} dangerouslySetInnerHTML={{ __html: html }} />;
}
