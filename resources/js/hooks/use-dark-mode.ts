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

        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    });

    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    const toggle = useCallback(() => setIsDark((prev) => !prev), []);

    return { isDark, toggle };
}
