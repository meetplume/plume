import { useScrollToAnchor } from '@/hooks/use-scroll-to-anchor';
import { normalizeCalloutSyntax, remarkCallouts } from '@/lib/remark-callouts';
import { useTexts } from '@/lib/texts';
import { createInlineSvgUrl } from '@expressive-code/core';
import { pluginCollapsibleSections } from '@expressive-code/plugin-collapsible-sections';
import { usePage } from '@inertiajs/react';
import type { Element, Root as HastRoot } from 'hast';
import type { Code, Root } from 'mdast';
import { useEffect, useMemo, useRef, useState } from 'react';
import rehypeExpressiveCode, {
    createRenderer,
    pluginFramesTexts,
    type RehypeExpressiveCodeRenderer,
    type ThemeObjectOrShikiThemeName,
} from 'rehype-expressive-code';
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

/** A link to another Markdown file of the vault, as written for GitHub: `setup.md`, `../guides/deploy.md#step-2`. */
const MARKDOWN_LINK = /^(?![a-z][a-z0-9+.-]*:|\/|#)([^?#]*)\.md([?#].*)?$/i;

/**
 * Points links to sibling Markdown files at their page: `guides/deploy.md`
 * becomes `/docs/guides/deploy`, resolved from the folder of the current file.
 */
function rehypeMarkdownLinks(pageBaseUrl: string) {
    return (tree: HastRoot) => {
        visit(tree, 'element', (node: Element) => {
            const href = node.properties?.href;
            if (node.tagName !== 'a' || typeof href !== 'string') return;

            const match = href.match(MARKDOWN_LINK);
            if (!match) return;

            const url = new URL(match[1], `https://plume.invalid${pageBaseUrl.replace(/\/$/, '')}/`);
            const path = url.pathname.replace(/\/index$/, '') || '/';
            node.properties.href = path + (match[2] ?? '');
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

const copyIcon = createInlineSvgUrl(
    `<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.25 5.25H7.25C6.14543 5.25 5.25 6.14543 5.25 7.25V14.25C5.25 15.3546 6.14543 16.25 7.25 16.25H14.25C15.3546 16.25 16.25 15.3546 16.25 14.25V7.25C16.25 6.14543 15.3546 5.25 14.25 5.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.80103 11.998L1.77203 5.07397C1.61003 3.98097 2.36403 2.96397 3.45603 2.80197L10.38 1.77297C11.313 1.63397 12.19 2.16297 12.528 3.00097" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
);

const codeRenderers = new Map<string, Promise<RehypeExpressiveCodeRenderer>>();

/**
 * One Expressive Code engine per pair of themes, shared by every page: building
 * it loads the themes and computes their styles, which no visit should repeat.
 */
function codeRenderer(light: string, dark: string, locale: string): Promise<RehypeExpressiveCodeRenderer> {
    const key = `${light}|${dark}|${locale}`;
    let renderer = codeRenderers.get(key);

    if (!renderer) {
        renderer = createRenderer({
            defaultLocale: locale,
            themes: [light as ThemeObjectOrShikiThemeName, dark as ThemeObjectOrShikiThemeName],
            themeCssSelector: (theme, { styleVariants }) => {
                const index = styleVariants.findIndex((v) => v.theme === theme);
                return index === 0 ? ':root:not(.dark)' : '.dark';
            },
            plugins: [pluginCollapsibleSections()],
            styleOverrides: {
                frames: { inlineButtonBorderOpacity: '0', copyIcon },
            },
        });
        renderer.catch(() => codeRenderers.delete(key));
        codeRenderers.set(key, renderer);
    }

    return renderer;
}

/**
 * Runs the callback once the browser has painted: the animation frame comes
 * just before the next paint, the timeout just after. A hidden page does not
 * paint, so the callback runs at once.
 */
function afterNextPaint(callback: () => void) {
    if (document.hidden) {
        callback();
        return;
    }

    requestAnimationFrame(() => setTimeout(callback));
}

export interface PlumePageContext {
    content: string;
    title?: string;
    description?: string;
    meta?: Record<string, unknown>;
    codeThemeLight?: string;
    codeThemeDark?: string;
    contentAssetBase?: string;
    pageBaseUrl?: string;
}

interface MarkdownRendererProps {
    page: PlumePageContext;
    className?: string;
}

export function MarkdownRenderer({ page, className }: MarkdownRendererProps) {
    const containerRef = useRef<HTMLDivElement>(null);
    const renderedContent = useRef<string | null>(null);
    // The last HTML shown, for the scroll to the address's anchor.
    const [shownHtml, setShownHtml] = useState('');

    const sharedProps = usePage().props as Record<string, unknown>;
    const locale = typeof sharedProps.locale === 'string' ? sharedProps.locale : 'en';
    const t = useTexts();
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

    const processors = useMemo(() => {
        pluginFramesTexts.overrideTexts(locale, {
            copyButtonTooltip: t('Copy to clipboard'),
            copyButtonCopied: t('Copied!'),
        });

        const build = (highlightCode: boolean) => {
            const pipeline = unified()
                .use(remarkParse)
                .use(remarkGfm)
                .use(remarkFrontmatter)
                .use(remarkGithubAdmonitionsToDirectives)
                .use(remarkDirective)
                .use(remarkCallouts, { detailsLabel: t('Details') })
                .use(remarkCodeMeta)
                .use(remarkRehype, { allowDangerousHtml: true });

            if (highlightCode) {
                pipeline.use(rehypeExpressiveCode, { customCreateRenderer: () => codeRenderer(codeThemeLight, codeThemeDark, locale) });
            }

            pipeline.use(rehypeRaw);

            if (page.contentAssetBase) {
                pipeline.use(() => rehypeContentAssets(page.contentAssetBase!));
            }

            if (page.pageBaseUrl) {
                pipeline.use(() => rehypeMarkdownLinks(page.pageBaseUrl!));
            }

            return pipeline
                .use(rehypeSlug)
                .use(rehypeExternalLinks, { target: '_blank', rel: ['noopener', 'noreferrer'] })
                .use(rehypeStringify);
        };

        return { text: build(false), highlighted: build(true) };
    }, [codeThemeLight, codeThemeDark, page.contentAssetBase, page.pageBaseUrl, locale, t]);

    // Highlighting the code holds the main thread for most of the rendering
    // time: a new page paints its text first. A theme change keeps the page
    // as it is until the code is highlighted again.
    useEffect(() => {
        const markdown = normalizeCalloutSyntax(page.content);
        let cancelled = false;

        const show = (html: string) => {
            const container = containerRef.current;

            if (cancelled || !container) return false;

            const range = document.createRange();
            range.selectNode(container);
            const fragment = range.createContextualFragment(html);

            container.innerHTML = '';
            container.append(fragment);
            setShownHtml(html);

            return true;
        };

        const highlight = () => {
            if (cancelled) return;

            processors.highlighted.process(markdown).then((file) => {
                if (show(String(file))) {
                    renderedContent.current = page.content;
                }
            });
        };

        if (renderedContent.current === page.content) {
            highlight();
        } else {
            processors.text.process(markdown).then((file) => {
                const html = String(file);

                if (!show(html)) return;

                // Without a code block, the highlighted page would be the same.
                if (html.includes('<pre')) {
                    afterNextPaint(highlight);
                } else {
                    renderedContent.current = page.content;
                }
            });
        }

        return () => {
            cancelled = true;
        };
    }, [processors, page.content]);

    useScrollToAnchor(shownHtml, page.content);

    return <div ref={containerRef} className={className} />;
}
