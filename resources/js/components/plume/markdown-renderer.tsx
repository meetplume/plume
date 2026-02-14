import { useMemo } from 'react';
import { MarkdownHooks } from 'react-markdown';
import rehypeExpressiveCode, { type RehypeExpressiveCodeOptions, type ThemeObjectOrShikiThemeName } from 'rehype-expressive-code';
import rehypeRaw from 'rehype-raw';
import rehypeSlug from 'rehype-slug';
import remarkFrontmatter from 'remark-frontmatter';
import remarkGfm from 'remark-gfm';

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
    const { content, codeThemeLight, codeThemeDark } = page;
    const rehypeExpressiveCodeOptions: RehypeExpressiveCodeOptions = useMemo(
        () => ({
            themes: [
                (codeThemeDark ?? 'github-dark') as ThemeObjectOrShikiThemeName,
                (codeThemeLight ?? 'github-light') as ThemeObjectOrShikiThemeName,
            ],
        }),
        [codeThemeLight, codeThemeDark],
    );

    return (
        <div className={className}>
            <MarkdownHooks
                remarkPlugins={[remarkGfm, remarkFrontmatter]}
                rehypePlugins={[rehypeRaw, [rehypeExpressiveCode, rehypeExpressiveCodeOptions], rehypeSlug]}
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
                {content}
            </MarkdownHooks>
        </div>
    );
}
