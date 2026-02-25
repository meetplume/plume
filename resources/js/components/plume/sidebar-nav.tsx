import { resolveIcon } from '@/lib/utils';
import { Link, router } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export interface NavPage {
    type: 'page';
    key: string;
    label: string;
    slug: string;
    href: string;
    hidden: boolean;
    active: boolean;
}

export interface NavGroupItem {
    type: 'group';
    key: string;
    label: string;
    icon: string | null;
    pages: NavPage[];
}

export type NavItem = NavGroupItem | NavPage;

export interface CollectionInfo {
    title: string | null;
    description: string | null;
}

export interface SidebarNavProps {
    navigation: NavItem[];
    collection?: CollectionInfo;
}

// Module-level: survives component unmount/remount between Inertia navigations
let savedScrollTop = 0;

router.on('before', () => {
    const el = document.getElementById('plume-sidebar-nav');
    if (el) {
        savedScrollTop = el.scrollTop;
    }
});

export function SidebarNav({ navigation }: SidebarNavProps) {
    const navRef = useRef<HTMLElement>(null);

    useEffect(() => {
        if (navRef.current) {
            navRef.current.scrollTop = savedScrollTop;
        }
    }, []);

    return (
        <nav id="plume-sidebar-nav" ref={navRef} className="sticky top-0 h-screen w-64 shrink-0 overflow-y-auto py-8 pr-4 pl-6 text-sm">
            <ul className="space-y-6">
                {navigation.map((item) => {
                    if (item.type === 'group') {
                        return <NavGroup key={item.key} group={item} />;
                    }
                    if (item.hidden) return null;
                    return (
                        <li key={item.key}>
                            <NavLink page={item} />
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}

function NavGroup({ group }: { group: NavGroupItem }) {
    const visiblePages = group.pages.filter((p) => !p.hidden);
    const Icon = group.icon ? resolveIcon(group.icon) : null;
    if (visiblePages.length === 0) return null;

    return (
        <li>
            <div className="flex items-center gap-2 px-4 py-1.5 font-semibold tracking-wide text-foreground">
                {Icon && <Icon className="size-3.5 shrink-0" />}
                {group.label}
            </div>
            <ul className="space-y-0.5">
                {visiblePages.map((page) => (
                    <li key={page.key}>
                        <NavLink page={page} />
                    </li>
                ))}
            </ul>
        </li>
    );
}

function NavLink({ page }: { page: NavPage }) {
    return (
        <Link
            href={page.href}
            className={`block rounded-xl px-4 py-1.5 leading-snug transition-colors ${
                page.active
                    ? 'dark:text-primary-light dark:bg-primary-light/10 bg-primary/10 text-primary [text-shadow:-0.2px_0_0_currentColor,0.2px_0_0_currentColor]'
                    : 'text-muted-foreground hover:bg-foreground/5 hover:text-foreground'
            }`}
        >
            {page.label}
        </Link>
    );
}
