import { useCallback, useEffect, useState } from 'react';

const ONE_YEAR = 60 * 60 * 24 * 365;

/** Set by the host through `Plume::configure()->appearanceKey()`, so both share one choice. */
function appearanceKey(): string {
    return document.documentElement.dataset.appearanceKey || 'theme';
}

export function useDarkMode() {
    const [isDark, setIsDark] = useState(() => {
        if (typeof window === 'undefined') {
            return false;
        }

        const stored = localStorage.getItem(appearanceKey());

        if (stored === 'dark' || stored === 'light') {
            return stored === 'dark';
        }

        if (document.documentElement.classList.contains('dark')) {
            return true;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    });

    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);

        if (window.__plumeTheme) {
            const vars = isDark ? window.__plumeTheme.dark : window.__plumeTheme.light;
            for (const [prop, value] of Object.entries(vars)) {
                document.documentElement.style.setProperty(prop, value);
            }
        }
    }, [isDark]);

    const toggle = useCallback(() => {
        setIsDark((prev) => {
            const next = !prev;
            const key = appearanceKey();
            const value = next ? 'dark' : 'light';

            localStorage.setItem(key, value);
            // The cookie lets the host render its own pages in the same mode on the server.
            document.cookie = `${key}=${value};path=/;max-age=${ONE_YEAR};SameSite=Lax`;

            return next;
        });
    }, []);

    return { isDark, toggle };
}
