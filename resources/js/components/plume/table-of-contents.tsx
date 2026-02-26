import { useEffect, useState } from 'react';

interface TocEntry {
    id: string;
    text: string;
    level: number;
}

interface TableOfContentsProps {
    contentSelector?: string;
}

export function TableOfContents({ contentSelector = 'article' }: TableOfContentsProps) {
    const [headings, setHeadings] = useState<TocEntry[]>([]);
    const [activeId, setActiveId] = useState<string>('');

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

        // The markdown is rendered async, so observe mutations
        const container = document.querySelector(contentSelector);
        if (!container) return;

        const mo = new MutationObserver(extract);
        mo.observe(container, { childList: true, subtree: true });
        extract();

        return () => mo.disconnect();
    }, [contentSelector]);

    // Scroll-spy
    useEffect(() => {
        if (headings.length === 0) return;

        const getActiveId = () => {
            // If scrolled to the bottom, activate the last heading
            const atBottom = window.innerHeight + window.scrollY >= document.body.scrollHeight - 40;
            if (atBottom) return headings[headings.length - 1].id;

            // Walk headings top-to-bottom; pick the last one whose top is above the threshold
            const threshold = 80;
            let current = headings[0]?.id ?? '';

            for (const heading of headings) {
                const el = document.getElementById(heading.id);
                if (!el) continue;
                if (el.getBoundingClientRect().top <= threshold) {
                    current = heading.id;
                } else {
                    break;
                }
            }

            return current;
        };

        const onScroll = () => {
            setActiveId(getActiveId());
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        return () => window.removeEventListener('scroll', onScroll);
    }, [headings]);

    if (headings.length === 0) return null;

    const minLevel = Math.min(...headings.map((h) => h.level));

    return (
        <nav aria-label="Table of contents">
            <div className="sticky top-0 z-10 h-8 bg-linear-to-b from-background"></div>
            <p className="px-4 py-1.5 font-semibold tracking-wide text-foreground">On this page</p>
            <ul className="space-y-0.5">
                {headings.map((heading) => (
                    <li key={heading.id} style={{ paddingLeft: `${(heading.level - minLevel) * 12}px` }}>
                        <a
                            href={`#${heading.id}`}
                            onClick={(e) => {
                                e.preventDefault();
                                document.getElementById(heading.id)?.scrollIntoView({ behavior: 'smooth' });
                                history.replaceState(null, '', `#${heading.id}`);
                            }}
                            className={`block rounded-[calc(var(--radius)-4px)] px-4 py-1 leading-snug transition-colors ${
                                activeId === heading.id
                                    ? 'dark:text-primary-light dark:bg-primary-light/10 bg-primary/10 text-primary [text-shadow:-0.2px_0_0_currentColor,0.2px_0_0_currentColor]'
                                    : 'text-muted-foreground hover:text-foreground'
                            }`}
                        >
                            {heading.text}
                        </a>
                    </li>
                ))}
            </ul>
        </nav>
    );
}