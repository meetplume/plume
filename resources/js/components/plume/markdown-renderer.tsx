import { normalizeCalloutSyntax, remarkCallouts } from '@/lib/remark-callouts';
import { createInlineSvgUrl } from '@expressive-code/core';
import { pluginCollapsibleSections } from '@expressive-code/plugin-collapsible-sections';
import { usePage } from '@inertiajs/react';
import type { Element, Root as HastRoot } from 'hast';
import type { Code, Root } from 'mdast';
import { useEffect, useMemo, useRef, useState } from 'react';
import rehypeExpressiveCode, { type ThemeObjectOrShikiThemeName } from 'rehype-expressive-code';
import rehypeExternalLinks from 'rehype-external-links';
import rehypeRaw from 'rehype-raw';
import rehypeSlug from 'rehype-slug';
import rehypeStringify from 'rehype-stringify';
import remarkDirective from 'remark-directive';
import remarkFrontmatter from 'remark-frontmatter';
import remarkGfm from 'remark-gfm';
import remarkGithubAdmonitionsToDirectives from 'remark-github-admonitions-to-directives';
import remarkParse from 'remark-parse';
import remarkRehype from 'remark-rehype';
import { unified } from 'unified';
import { visit } from 'unist-util-visit';

function isRelativePath(src: string): boolean {
    return !src.startsWith('/') && !src.startsWith('http://') && !src.startsWith('https://') && !src.startsWith('data:') && !src.startsWith('#');
}

/**
 * Rewrites relative image/video/source src attributes to point to the content asset route.
 */
function rehypeContentAssets(assetBase: string) {
    return (tree: HastRoot) => {
        visit(tree, 'element', (node: Element) => {
            const src = node.properties?.src;
            if (node.tagName === 'img' && typeof src === 'string' && isRelativePath(src)) {
                node.properties.src = `${assetBase}/${src}`;
            }
        });
    };
}

/**
 * Splits meta from lang in code blocks so ```js{3-5} works like ```js {3-5}.
 */
function remarkCodeMeta() {
    return (tree: Root) => {
        visit(tree, 'code', (node: Code) => {
            if (!node.lang) return;
            const match = node.lang.match(/^([^{]*?)(\{.+}.*)$/);
            if (match) {
                node.lang = match[1] || undefined!;
                node.meta = node.meta ? `${match[2]} ${node.meta}` : match[2];
            }
        });
    };
}

export interface PlumePageContext {
    content: string;
    title?: string;
    description?: string;
    meta?: Record<string, unknown>;
    codeThemeLight?: string;
    codeThemeDark?: string;
    contentAssetBase?: string;
}

interface MarkdownRendererProps {
    page: PlumePageContext;
    className?: string;
}

export function MarkdownRenderer({ page, className }: MarkdownRendererProps) {
    const [html, setHtml] = useState('');
    const containerRef = useRef<HTMLDivElement>(null);

    const sharedProps = usePage().props as Record<string, unknown>;
    const plume = sharedProps.plume as { codeThemeLight?: string; codeThemeDark?: string } | undefined;

    const initialLight = page.codeThemeLight ?? plume?.codeThemeLight ?? 'github-light';
    const initialDark = page.codeThemeDark ?? plume?.codeThemeDark ?? 'github-dark';

    const [codeThemeLight, setCodeThemeLight] = useState(initialLight);
    const [codeThemeDark, setCodeThemeDark] = useState(initialDark);

    useEffect(() => {
        const handler = (e: Event) => {
            const { light, dark } = (e as CustomEvent<{ light: string; dark: string }>).detail;
            setCodeThemeLight(light);
            setCodeThemeDark(dark);
        };
        window.addEventListener('plume:code-theme', handler);
        return () => window.removeEventListener('plume:code-theme', handler);
    }, []);

    const processor = useMemo(() => {
        const pipeline = unified()
            .use(remarkParse)
            .use(remarkGfm)
            .use(remarkFrontmatter)
            .use(remarkGithubAdmonitionsToDirectives)
            .use(remarkDirective)
            .use(remarkCallouts)
            .use(remarkCodeMeta)
            .use(remarkRehype, { allowDangerousHtml: true })
            .use(rehypeExpressiveCode, {
                themes: [codeThemeLight as ThemeObjectOrShikiThemeName, codeThemeDark as ThemeObjectOrShikiThemeName],
                themeCssSelector: (theme, { styleVariants }) => {
                    const index = styleVariants.findIndex((v) => v.theme === theme);
                    return index === 0 ? ':root:not(.dark)' : '.dark';
                },
                plugins: [pluginCollapsibleSections()],
                styleOverrides: {
                    frames: {
                        copyIcon: createInlineSvgUrl(
                            `<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.25 5.25H7.25C6.14543 5.25 5.25 6.14543 5.25 7.25V14.25C5.25 15.3546 6.14543 16.25 7.25 16.25H14.25C15.3546 16.25 16.25 15.3546 16.25 14.25V7.25C16.25 6.14543 15.3546 5.25 14.25 5.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.80103 11.998L1.77203 5.07397C1.61003 3.98097 2.36403 2.96397 3.45603 2.80197L10.38 1.77297C11.313 1.63397 12.19 2.16297 12.528 3.00097" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
                        ),
                    },
                },
            })
            .use(rehypeRaw);

        if (page.contentAssetBase) {
            pipeline.use(() => rehypeContentAssets(page.contentAssetBase!));
        }

        return pipeline
            .use(rehypeSlug)
            .use(rehypeExternalLinks, { target: '_blank', rel: ['noopener', 'noreferrer'] })
            .use(rehypeStringify);
    }, [codeThemeLight, codeThemeDark, page.contentAssetBase]);

    useEffect(() => {
        processor.process(normalizeCalloutSyntax(page.content)).then((file) => {
            setHtml(String(file));
        });
    }, [processor, page.content]);

    useEffect(() => {
        if (!containerRef.current || !html) return;

        const range = document.createRange();
        range.selectNode(containerRef.current);
        const fragment = range.createContextualFragment(html);

        containerRef.current.innerHTML = '';
        containerRef.current.append(fragment);
    }, [html]);

    return <div ref={containerRef} className={className} />;
}
