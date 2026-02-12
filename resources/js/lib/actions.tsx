import type { VariantProps } from 'class-variance-authority';
import type { icons } from 'lucide-react';

import { Button, buttonVariants } from '@/components/ui/button';
import * as LucideIcons from 'lucide-react';

export type Action = {
    label: string;
    href: string;
    target?: string;
    variant?: VariantProps<typeof buttonVariants>['variant'];
    icon?: keyof typeof icons;
};

export function renderActions(actions: Action[]) {
    return actions.map((action) => {
        const Icon = action.icon ? LucideIcons[action.icon] : null;

        return (
            <Button key={action.label} variant={action.variant}>
                <a href={action.href} target={action.target} className="flex items-center gap-2">
                    {action.label}
                    {Icon && <Icon />}
                </a>
            </Button>
        );
    });
}
