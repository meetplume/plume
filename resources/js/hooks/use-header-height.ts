import { type RefObject, useLayoutEffect } from 'react';

/**
 * Publishes the header's height as `--plume-header-height` on <html>: the
 * sidebars stick right under the header, and headings scroll to just below it,
 * whatever the header holds (a taller logo, a row of tabs).
 */
export function useHeaderHeight(header: RefObject<HTMLElement | null>): void {
    useLayoutEffect(() => {
        const element = header.current;

        if (!element) return;

        const publish = () => document.documentElement.style.setProperty('--plume-header-height', `${element.getBoundingClientRect().height}px`);

        publish();

        const observer = new ResizeObserver(publish);
        observer.observe(element);

        return () => observer.disconnect();
    }, [header]);
}
