import { useEffect, useState } from 'react';

function isPlatformMac(): boolean {
    if (typeof navigator === 'undefined') return false;
    return /Mac|iPod|iPhone|iPad/.test(navigator.platform);
}

type SearchShortcut = {
    open: boolean;
    setOpen: (open: boolean) => void;
    toggle: () => void;
    shortcutLabel: string;
};

export function useSearchShortcut(enabled: boolean): SearchShortcut {
    const [open, setOpen] = useState(false);
    const [shortcutLabel, setShortcutLabel] = useState('Ctrl K');

    useEffect(() => {
        setShortcutLabel(isPlatformMac() ? '⌘ K' : 'Ctrl K');
    }, []);

    useEffect(() => {
        if (!enabled) return undefined;

        const handler = (event: KeyboardEvent) => {
            if (!(event.metaKey || event.ctrlKey) || event.key.toLowerCase() !== 'k') return;
            event.preventDefault();
            setOpen((current) => !current);
        };

        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [enabled]);

    return { open, setOpen, toggle: () => setOpen((current) => !current), shortcutLabel };
}
