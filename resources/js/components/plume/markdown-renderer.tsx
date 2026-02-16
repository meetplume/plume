import { MarkdownHooks } from 'react-markdown';
import rehypeRaw from 'rehype-raw';
import rehypeSlug from 'rehype-slug';
import remarkFrontmatter from 'remark-frontmatter';
import remarkGfm from 'remark-gfm';

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

export function MarkdownRenderer({ page, className }: MarkdownRendererProps) {
    return (
        <div className={className}>
            <MarkdownHooks
                remarkPlugins={[remarkGfm, remarkFrontmatter]}
                rehypePlugins={[rehypeRaw, rehypeSlug]}
                components={{
                    a: ({ href, children, ...props }) => {
                        const isExternal = href?.startsWith('http');
                        return (
                            <a href={href} {...(isExternal ? { target: '_blank', rel: 'noopener noreferrer' } : {})} {...props}>
                                {children}
                            </a>
                        );
                    },
                }}
            >
                {page.content}
            </MarkdownHooks>
        </div>
    );
}
