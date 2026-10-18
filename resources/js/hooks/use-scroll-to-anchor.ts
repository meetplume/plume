import { useEffect, useRef } from 'react';

/**
 * The browser looks for the address's #anchor while the page's text is still
 * being rendered, finds nothing and stays at the top. Scrolls there with the
 * first text of each page; a later render of the same page (another code
 * theme) leaves the reader where they are.
 */
export function useScrollToAnchor(html: string, content: string) {
    const pending = useRef(false);

    useEffect(() => {
        pending.current = true;
    }, [content]);

    useEffect(() => {
        if (!html || !pending.current) return;

        pending.current = false;

        const id = decodeURIComponent(window.location.hash.slice(1));

        if (id) {
            document.getElementById(id)?.scrollIntoView();
        }
    }, [html]);
}
