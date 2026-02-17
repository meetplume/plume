import type { icons } from 'lucide-react';

export type Feature = {
    title: string;
    description: string;
    icon?: keyof typeof icons;
};
