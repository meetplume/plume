import { type ClassValue, clsx } from 'clsx';
import type { LucideIcon } from 'lucide-react';
import * as LucideIcons from 'lucide-react';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function pascalCase(value: string): string {
    return value
        .split(/[-_\s]+/)
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join('');
}

export function resolveIcon(name: string): LucideIcon | null {
    if (name in LucideIcons) {
        return LucideIcons[name as keyof typeof LucideIcons] as LucideIcon;
    }

    const normalized = pascalCase(name);

    if (normalized in LucideIcons) {
        return LucideIcons[normalized as keyof typeof LucideIcons] as LucideIcon;
    }

    return null;
}
