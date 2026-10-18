import { router } from '@inertiajs/react';

/**
 * A page opened with `?embed` shows its content alone, for an app to show it
 * in a drawer or a dialog through an iframe. Read when the document loads: the
 * following visits stay embedded without carrying the parameter.
 */
export const embedded = typeof window !== 'undefined' && new URLSearchParams(window.location.search).has('embed');

const SCALES = ['sm', 'base', 'lg'] as const;

export type EmbedScale = (typeof SCALES)[number];

/**
 * The type scale of an embedded page, from `&scale=` (`sm`, `base`, `lg`).
 * `sm` by default: a panel sits beside an app whose own text is smaller than
 * a documentation page's.
 */
export const embedScale: EmbedScale = (() => {
    const scale = typeof window === 'undefined' ? null : new URLSearchParams(window.location.search).get('scale');

    return SCALES.find((candidate) => candidate === scale) ?? 'sm';
})();

/** What an embedded page tells the app around it, on each page it shows. */
export interface EmbeddedPageMessage {
    type: 'plume:page';
    title: string | null;
    url: string;
}

/**
 * The app that embeds the page: the origin of the page that loaded the frame.
 * Without a referrer, the page's own origin, so nothing leaks to another site.
 */
function hostOrigin(): string {
    try {
        return new URL(document.referrer).origin;
    } catch {
        return window.location.origin;
    }
}

export function tellHost(title: string | null): void {
    if (window.parent === window) return;

    const message: EmbeddedPageMessage = {
        type: 'plume:page',
        title,
        url: window.location.pathname + window.location.hash,
    };

    window.parent.postMessage(message, hostOrigin());
}

function isVaultPage(url: URL, prefix: string): boolean {
    const root = '/' + prefix.replace(/^\/+|\/+$/g, '');
    const path = url.pathname.replace(/\/+$/, '') || '/';

    if (root === '/') return !path.startsWith('/_');

    return path === root || (path.startsWith(root + '/') && !path.startsWith(root + '/_'));
}

/**
 * Keeps the reader inside the frame for the vault's pages, through Inertia and
 * without adding to the app's history, and opens any other page of the site in
 * the whole window: a link to the app must not load the app inside the frame.
 */
export function followLinksFromFrame(prefix: string): () => void {
    const onClick = (event: MouseEvent) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        const anchor = (event.target as Element | null)?.closest('a');

        if (!anchor || !anchor.href || anchor.hasAttribute('download') || (anchor.target !== '' && anchor.target !== '_self')) return;

        const url = new URL(anchor.href);

        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.hash !== '') return;

        event.preventDefault();

        if (isVaultPage(url, prefix)) {
            router.visit(url.pathname + url.search + url.hash, { replace: true });
        } else {
            window.open(url.href, '_top');
        }
    };

    document.addEventListener('click', onClick);

    return () => document.removeEventListener('click', onClick);
}
