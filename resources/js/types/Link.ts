import { buttonVariants } from '@/components/ui/button';
import type { VariantProps } from 'class-variance-authority';
import type { icons } from 'lucide-react';

export type Link = {
    className?: string;
    label: string;
    href: string;
    target?: string;
    variant?: VariantProps<typeof buttonVariants>['variant'];
    icon?: keyof typeof icons;
};
