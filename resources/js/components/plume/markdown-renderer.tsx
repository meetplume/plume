import { normalizeCalloutSyntax, remarkCallouts } from '@/lib/remark-callouts';
import { pluginCollapsibleSections } from '@expressive-code/plugin-collapsible-sections';
import { usePage } from '@inertiajs/react';
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

    const processor = useMemo(
        () =>
            unified()
                .use(remarkParse)
                .use(remarkGfm)
                .use(remarkFrontmatter)
                .use(remarkGithubAdmonitionsToDirectives)
                .use(remarkDirective)
                .use(remarkCallouts)
                .use(remarkCodeMeta)
                .use(remarkRehype, { allowDangerousHtml: true })
                .use(rehypeExpressiveCode, {
                    themes: [codeThemeDark as ThemeObjectOrShikiThemeName, codeThemeLight as ThemeObjectOrShikiThemeName],
                    plugins: [pluginCollapsibleSections()],
                })
                .use(rehypeRaw)
                .use(rehypeSlug)
                .use(rehypeExternalLinks, { target: '_blank', rel: ['noopener', 'noreferrer'] })
                .use(rehypeStringify),
        [codeThemeLight, codeThemeDark],
    );

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
