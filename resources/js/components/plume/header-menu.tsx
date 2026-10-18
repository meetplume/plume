import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { Check, ChevronDown, type LucideIcon } from 'lucide-react';
import { DropdownMenu } from 'radix-ui';

export type HeaderMenuItem = {
    key: string;
    label: string;
    href: string;
    active: boolean;
    /** For an item that names a language: that language, so it is read and announced in it. */
    lang?: string;
};

type HeaderMenuProps = {
    /** What the menu picks ("Language", "Version"), announced before the current choice. */
    label: string;
    icon: LucideIcon;
    items: HeaderMenuItem[];
    /** `icon` shows the icon alone, as in the mobile menu next to the light/dark switch. */
    variant?: 'label' | 'icon';
    /** Called once a choice is picked, e.g. to close the mobile menu. */
    onNavigate?: () => void;
};

/**
 * A compact menu of the header (language, version): the current choice as the
 * trigger, and links to the other choices, visited through Inertia.
 */
export function HeaderMenu({ label, icon: Icon, items, variant = 'label', onNavigate }: HeaderMenuProps) {
    const current = items.find((item) => item.active);

    return (
        <DropdownMenu.Root modal={false}>
            <DropdownMenu.Trigger className="inline-flex items-center gap-1.5 rounded-md text-sm text-muted-foreground outline-hidden hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring data-[state=open]:text-foreground">
                <Icon className={cn('shrink-0', variant === 'icon' ? 'size-6' : 'size-4')} />
                <span className="sr-only">{label}, </span>
                <span lang={current?.lang} className={cn(variant === 'icon' && 'sr-only')}>
                    {current?.label}
                </span>
                {variant === 'label' && <ChevronDown className="size-3.5 shrink-0 opacity-60" />}
            </DropdownMenu.Trigger>
            <DropdownMenu.Portal>
                <DropdownMenu.Content
                    align="end"
                    sideOffset={10}
                    className="z-60 min-w-40 rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0"
                >
                    {items.map((item) => (
                        <DropdownMenu.Item key={item.key} asChild>
                            <Link
                                href={item.href}
                                lang={item.lang}
                                hrefLang={item.lang}
                                aria-current={item.active ? 'true' : undefined}
                                onClick={onNavigate}
                                className={cn(
                                    'flex cursor-pointer items-center justify-between gap-4 rounded-sm px-3 py-1.5 text-sm outline-hidden hover:bg-accent hover:text-accent-foreground data-highlighted:bg-accent data-highlighted:text-accent-foreground',
                                    item.active ? 'font-medium text-foreground' : 'text-muted-foreground',
                                )}
                            >
                                {item.label}
                                {item.active && <Check className="size-4 shrink-0 text-primary" />}
                            </Link>
                        </DropdownMenu.Item>
                    ))}
                </DropdownMenu.Content>
            </DropdownMenu.Portal>
        </DropdownMenu.Root>
    );
}
