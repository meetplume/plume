import { useTexts } from '@/lib/texts';
import { TextAlignStart } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

interface TocEntry {
    id: string;
    text: string;
    level: number;
}

interface TableOfContentsProps {
    contentSelector?: string;
}

/** Room kept around the active entries, which also clears the fade at the top of the panel. */
const FOLLOW_MARGIN = 48;

function scrollParent(element: HTMLElement): HTMLElement | null {
    for (let parent = element.parentElement; parent; parent = parent.parentElement) {
        const { overflowY } = getComputedStyle(parent);

        if ((overflowY === 'auto' || overflowY === 'scroll') && parent.scrollHeight > parent.clientHeight) {
            return parent;
        }
    }

    return null;
}

export function TableOfContents({ contentSelector = 'article' }: TableOfContentsProps) {
    const t = useTexts();
    const [headings, setHeadings] = useState<TocEntry[]>([]);
    const [activeIds, setActiveIds] = useState<Set<string>>(new Set());
    const [indicator, setIndicator] = useState<{ top: number; height: number } | null>(null);
    const itemRefs = useRef<Map<string, HTMLLIElement>>(new Map());
    const listRef = useRef<HTMLUListElement>(null);

    // Extract headings from the rendered content
    useEffect(() => {
        const extract = () => {
            const container = document.querySelector(contentSelector);
            if (!container) return;

            const nodes = container.querySelectorAll('h2[id], h3[id]');
            const entries: TocEntry[] = Array.from(nodes).map((el) => ({
                id: el.id,
                text: el.textContent?.trim() ?? '',
                level: parseInt(el.tagName[1], 10),
            }));

            setHeadings(entries);
        };

        const container = document.querySelector(contentSelector);
        if (!container) return;

        const mo = new MutationObserver(extract);
        mo.observe(container, { childList: true, subtree: true });
        extract();

        return () => mo.disconnect();
    }, [contentSelector]);

    // Update the indicator position based on active items
    const updateIndicator = useCallback(() => {
        if (!listRef.current) return;

        const listRect = listRef.current.getBoundingClientRect();
        let minTop = Infinity;
        let maxBottom = -Infinity;

        for (const id of activeIds) {
            const el = itemRefs.current.get(id);
            if (!el) continue;
            const rect = el.getBoundingClientRect();
            minTop = Math.min(minTop, rect.top - listRect.top);
            maxBottom = Math.max(maxBottom, rect.bottom - listRect.top);
        }

        if (minTop !== Infinity) {
            setIndicator({ top: minTop, height: maxBottom - minTop });
        } else {
            setIndicator(null);
        }
    }, [activeIds]);

    useEffect(() => {
        updateIndicator();
    }, [updateIndicator]);

    // A long table of contents scrolls in its own panel: keep the active entries in
    // view as the page scrolls, unless the reader is browsing the panel itself.
    const indicatorTop = indicator?.top;
    const indicatorHeight = indicator?.height;

    useEffect(() => {
        if (indicatorTop === undefined || indicatorHeight === undefined || !listRef.current) return;

        const panel = scrollParent(listRef.current);
        if (!panel || panel.matches(':hover')) return;

        const listTop = listRef.current.getBoundingClientRect().top - panel.getBoundingClientRect().top + panel.scrollTop;
        const top = listTop + indicatorTop;
        const bottom = top + indicatorHeight;

        if (top >= panel.scrollTop + FOLLOW_MARGIN && bottom <= panel.scrollTop + panel.clientHeight - FOLLOW_MARGIN) return;

        const fits = indicatorHeight <= panel.clientHeight - 2 * FOLLOW_MARGIN;
        const target = fits ? top - (panel.clientHeight - indicatorHeight) / 2 : top - FOLLOW_MARGIN;

        panel.scrollTo({ top: Math.max(0, target), behavior: 'smooth' });
    }, [indicatorTop, indicatorHeight]);

    // Scroll-spy: track all headings visible in viewport
    useEffect(() => {
        if (headings.length === 0) return;

        const getActiveIds = (): Set<string> => {
            const viewportTop = 80;
            const viewportBottom = window.innerHeight;
            const active = new Set<string>();

            // If at the bottom, activate from last heading onward
            const atBottom = window.innerHeight + window.scrollY >= document.body.scrollHeight - 40;
            if (atBottom) {
                active.add(headings[headings.length - 1].id);
                return active;
            }

            for (let i = 0; i < headings.length; i++) {
                const el = document.getElementById(headings[i].id);
                if (!el) continue;

                const headingTop = el.getBoundingClientRect().top;
                // The "section" for this heading extends to the next heading
                const nextEl = i < headings.length - 1 ? document.getElementById(headings[i + 1].id) : null;
                const sectionBottom = nextEl ? nextEl.getBoundingClientRect().top : document.body.getBoundingClientRect().bottom;

                // Section is visible if it overlaps the viewport zone
                if (sectionBottom > viewportTop && headingTop < viewportBottom) {
                    active.add(headings[i].id);
                }
            }

            // Always have at least the current heading active
            if (active.size === 0 && headings.length > 0) {
                let current = headings[0].id;
                for (const heading of headings) {
                    const el = document.getElementById(heading.id);
                    if (!el) continue;
                    if (el.getBoundingClientRect().top <= viewportTop) {
                        current = heading.id;
                    } else {
                        break;
                    }
                }
                active.add(current);
            }

            return active;
        };

        const onScroll = () => {
            setActiveIds(getActiveIds());
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        return () => window.removeEventListener('scroll', onScroll);
    }, [headings]);

    if (headings.length === 0) return null;

    const minLevel = Math.min(...headings.map((h) => h.level));

    return (
        <nav aria-label={t('Table of contents')}>
            <div className="sticky top-0 z-10 h-8 bg-linear-to-b from-background"></div>
            <div className="flex items-center gap-2 pt-1.5 pr-4 pb-2">
                <TextAlignStart className="size-4 shrink-0 text-muted-foreground" />
                <p className="font-semibold tracking-wide text-foreground">{t('On this page')}</p>
            </div>
            <ul ref={listRef} className="relative border-l border-border">
                {/* Animated active indicator */}
                {indicator && (
                    <span
                        className="dark:bg-primary-light absolute -left-px w-0.5 rounded-full bg-primary transition-all duration-100 ease-in-out"
                        style={{ top: indicator.top, height: indicator.height }}
                    />
                )}
                {headings.map((heading) => {
                    const isActive = activeIds.has(heading.id);
                    return (
                        <li
                            key={heading.id}
                            ref={(el) => {
                                if (el) itemRefs.current.set(heading.id, el);
                                else itemRefs.current.delete(heading.id);
                            }}
                        >
                            <a
                                href={`#${heading.id}`}
                                onClick={(e) => {
                                    e.preventDefault();
                                    document.getElementById(heading.id)?.scrollIntoView();
                                    history.replaceState(null, '', `#${heading.id}`);
                                }}
                                className={`block py-1 leading-snug transition-colors ${
                                    isActive ? 'dark:text-primary-light text-primary' : 'text-muted-foreground hover:text-foreground'
                                }`}
                                style={{ paddingLeft: `${16 + (heading.level - minLevel) * 12}px` }}
                            >
                                {heading.text}
                            </a>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}
