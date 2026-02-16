import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { type Action } from '@/types/Action';
import * as LucideIcons from 'lucide-react';

export function Action({ ...props }: Action) {
    const Icon = props.icon ? LucideIcons[props.icon] : null;

    return (
        <Button variant={props.variant} className={cn(props.variant === 'link' ? 'px-0' : undefined, props.className)}>
            <a href={props.href} target={props.target} className="flex items-center gap-2">
                {props.label}
                {Icon && <Icon />}
            </a>
        </Button>
    );
}
