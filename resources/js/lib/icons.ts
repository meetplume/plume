import type { LucideIcon } from 'lucide-react';
import dynamicIconImports from 'lucide-react/dynamicIconImports';
import { useEffect, useState } from 'react';

const cache = new Map<string, LucideIcon>();

function toKebabCase(value: string): string {
    return value
        .replace(/([a-z])([A-Z])/g, '$1-$2')
        .replace(/[\s_]+/g, '-')
        .toLowerCase();
}

export function useIcon(name: string | undefined | null): LucideIcon | null {
    const [Icon, setIcon] = useState<LucideIcon | null>(() => (name ? cache.get(toKebabCase(name)) ?? null : null));

    useEffect(() => {
        if (!name) return;

        const kebab = toKebabCase(name);

        if (cache.has(kebab)) {
            setIcon(cache.get(kebab)!);
            return;
        }

        const loader = dynamicIconImports[kebab as keyof typeof dynamicIconImports];
        if (!loader) return;

        loader().then((mod) => {
            const icon = mod.default as LucideIcon;
            cache.set(kebab, icon);
            setIcon(icon);
        });
    }, [name]);

    return Icon;
}
