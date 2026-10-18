import * as React from 'react';
import { createPortal } from 'react-dom';

import { type HeaderMenuItem, HeaderMenu } from '@/components/plume/header-menu';
import { SearchButton } from '@/components/plume/search-button';
import { SearchDialog } from '@/components/plume/search-dialog';
import { type NavItem, NavTree } from '@/components/plume/sidebar-nav';
import { useDarkMode } from '@/hooks/use-dark-mode';
import { useSearchShortcut } from '@/hooks/use-search-shortcut';
import { useTexts } from '@/lib/texts';
import { cn } from '@/lib/utils';
import { Languages, Menu, Moon, Sun, Tag, X as XIcon } from 'lucide-react';
import { siBluesky, siDiscord, siGithub, siX, siYoutube } from 'simple-icons';

type Social = {
    url: string;
    icon: string;
};

type NavLink = {
    label: string;
    href: string;
};

type TabItem = {
    key: string;
    label: string;
    icon: string | null;
    href: string;
    active: boolean;
};

type VersionItem = {
    key: string;
    href: string;
    active: boolean;
    default: boolean;
};

type LanguageItem = {
    code: string;
    name: string;
    href: string;
    active: boolean;
};

export type DocsHeaderProps = {
    collectionTitle?: string | null;
    logo?: string | null;
    logoDark?: string | null;
    homeUrl?: string;
    links?: NavLink[];
    socials?: Social[];
    tabs?: TabItem[];
    versions?: VersionItem[];
    languages?: LanguageItem[];
    searchIndexUrl?: string | null;
    /** The sidebar navigation, which the mobile menu lists below the `lg` breakpoint. */
    navigation?: NavItem[];
};

const icons: Record<string, { path: string; title: string }> = {
    bluesky: siBluesky,
    discord: siDiscord,
    github: siGithub,
    x: siX,
    youtube: siYoutube,
};

function SocialIcon({ icon, className }: { icon: string; className?: string }) {
    const data = icons[icon];

    if (!data) {
        return null;
    }

    return (
        <svg className={cn('h-5 w-5 fill-current', className)} viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <title>{data.title}</title>
            <path d={data.path} />
        </svg>
    );
}

export function DocsHeader({
    collectionTitle,
    logo,
    logoDark,
    homeUrl = '/',
    links = [],
    socials = [],
    tabs,
    versions,
    languages,
    searchIndexUrl,
    navigation,
}: DocsHeaderProps) {
    const { isDark, toggle } = useDarkMode();
    const t = useTexts();
    const [mobileOpen, setMobileOpen] = React.useState(false);
    const search = useSearchShortcut(Boolean(searchIndexUrl));

    const versionItems: HeaderMenuItem[] =
        versions && versions.length > 1 ? versions.map((v) => ({ key: v.key, label: v.key, href: v.href, active: v.active })) : [];
    const languageItems: HeaderMenuItem[] =
        languages && languages.length > 1 ? languages.map((l) => ({ key: l.code, label: l.name, href: l.href, active: l.active, lang: l.code })) : [];

    return (
        <header data-slot="docs-header" className="sticky top-0 z-30 w-full border-b border-border/40 bg-background/80 py-4 backdrop-blur-sm">
            <div className="mx-auto flex max-w-368 items-center gap-6 px-6 lg:px-12">
                <a href={homeUrl} className="inline-flex shrink-0 items-center">
                    {logo ? (
                        logoDark ? (
                            <>
                                <img className="h-8 dark:hidden" loading="lazy" src={logo} alt={collectionTitle ?? 'Logo'} />
                                <img className="hidden h-8 dark:block" loading="lazy" src={logoDark} alt={collectionTitle ?? 'Logo'} />
                            </>
                        ) : (
                            <img className="h-8" loading="lazy" src={logo} alt={collectionTitle ?? 'Logo'} />
                        )
                    ) : (
                        <span className="flex h-8 items-center text-lg font-semibold text-foreground">{collectionTitle}</span>
                    )}
                </a>

                <div className="hidden flex-1 items-center justify-end gap-6 lg:flex">
                    {searchIndexUrl && <SearchButton onClick={() => search.setOpen(true)} shortcut={search.shortcutLabel} />}

                    {versionItems.length > 0 && <HeaderMenu label={t('Version')} icon={Tag} items={versionItems} />}

                    {languageItems.length > 0 && <HeaderMenu label={t('Language')} icon={Languages} items={languageItems} />}

                    <button onClick={toggle} className="text-muted-foreground hover:text-foreground" aria-label={t('Toggle dark mode')}>
                        {isDark ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
                    </button>

                    {links.map((link) => (
                        <a key={link.href} href={link.href} className="text-sm font-medium text-muted-foreground hover:text-foreground">
                            {link.label}
                        </a>
                    ))}

                    {socials.length > 0 && (
                        <div className="flex items-center gap-3">
                            {socials.map((social) => (
                                <a
                                    key={social.url}
                                    href={social.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-muted-foreground hover:text-foreground"
                                >
                                    <SocialIcon icon={social.icon} />
                                </a>
                            ))}
                        </div>
                    )}
                </div>

                <div className="flex flex-1 items-center justify-end gap-4 lg:hidden">
                    {searchIndexUrl && <SearchButton onClick={() => search.setOpen(true)} variant="icon" />}

                    <button onClick={() => setMobileOpen(true)} className="text-muted-foreground hover:text-foreground" aria-label={t('Open menu')}>
                        <Menu className="size-6 shrink-0" />
                    </button>
                </div>
            </div>

            {tabs && tabs.length > 0 && (
                <nav className="mx-auto flex max-w-368 items-center gap-1 px-6 pt-2 lg:px-12">
                    {tabs.map((tab) => (
                        <a
                            key={tab.key}
                            href={tab.href}
                            className={cn(
                                'rounded-md px-3 py-1.5 text-sm font-medium',
                                tab.active ? 'bg-accent text-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground',
                            )}
                        >
                            {tab.label}
                        </a>
                    ))}
                </nav>
            )}

            {mobileOpen &&
                createPortal(
                    <>
                        <div className="fixed inset-0 z-40 h-screen bg-black/10 backdrop-blur-xs lg:hidden" onClick={() => setMobileOpen(false)} />
                        <aside className="fixed inset-y-0 right-0 z-50 flex h-screen w-72 flex-col overflow-y-auto border-l border-black/10 bg-background px-6 py-4 shadow-lg lg:hidden dark:border-white/10">
                            <div className="flex h-8 items-center justify-end gap-3">
                                {versionItems.length > 0 && (
                                    <HeaderMenu
                                        variant="icon"
                                        label={t('Version')}
                                        icon={Tag}
                                        items={versionItems}
                                        onNavigate={() => setMobileOpen(false)}
                                    />
                                )}
                                {languageItems.length > 0 && (
                                    <HeaderMenu
                                        variant="icon"
                                        label={t('Language')}
                                        icon={Languages}
                                        items={languageItems}
                                        onNavigate={() => setMobileOpen(false)}
                                    />
                                )}
                                <button onClick={toggle} className="text-muted-foreground hover:text-foreground" aria-label={t('Toggle dark mode')}>
                                    {isDark ? <Sun className="size-6" /> : <Moon className="size-6" />}
                                </button>
                                <button
                                    onClick={() => setMobileOpen(false)}
                                    className="size-7 text-muted-foreground hover:text-foreground"
                                    aria-label={t('Close menu')}
                                >
                                    <XIcon className="size-7 shrink-0" />
                                </button>
                            </div>

                            {navigation && navigation.length > 0 && (
                                <nav className="-mx-1 mt-6 text-sm">
                                    <NavTree navigation={navigation} onNavigate={() => setMobileOpen(false)} />
                                </nav>
                            )}

                            <nav className="mt-6 flex flex-col gap-1">
                                {links.map((link) => (
                                    <a
                                        key={link.href}
                                        href={link.href}
                                        className="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                                    >
                                        {link.label}
                                    </a>
                                ))}
                            </nav>

                            {socials.length > 0 && (
                                <div className="mt-4 flex items-center gap-3 px-3">
                                    {socials.map((social) => (
                                        <a
                                            key={social.url}
                                            href={social.url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="text-muted-foreground hover:text-foreground"
                                        >
                                            <SocialIcon icon={social.icon} />
                                        </a>
                                    ))}
                                </div>
                            )}
                        </aside>
                    </>,
                    document.body,
                )}

            {searchIndexUrl && <SearchDialog open={search.open} onOpenChange={search.setOpen} indexUrl={searchIndexUrl} />}
        </header>
    );
}
