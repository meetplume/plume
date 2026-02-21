import { usePage } from '@inertiajs/react';
import { useEffect, useMemo, useRef, useState } from 'react';
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
                .use(remarkRehype, { allowDangerousHtml: true })
                .use(rehypeRaw)
                .use(rehypeExpressiveCode, {
                    themes: [codeThemeDark as ThemeObjectOrShikiThemeName, codeThemeLight as ThemeObjectOrShikiThemeName],
                })
                .use(rehypeSlug)
                .use(rehypeExternalLinks, { target: '_blank', rel: ['noopener', 'noreferrer'] })
                .use(rehypeStringify),
        [codeThemeLight, codeThemeDark],
    );

    useEffect(() => {
        processor.process(page.content).then((file) => {
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
