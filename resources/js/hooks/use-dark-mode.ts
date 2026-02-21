import { useCallback, useEffect, useState } from 'react';

export function useDarkMode() {
    const [isDark, setIsDark] = useState(() => {
        if (typeof window === 'undefined') {
            return false;
        }

        const stored = localStorage.getItem('theme');

        if (stored) {
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
            localStorage.setItem('theme', next ? 'dark' : 'light');
            return next;
        });
    }, []);

    return { isDark, toggle };
}
