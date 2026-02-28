import * as React from 'react';

import { useDarkMode } from '@/hooks/use-dark-mode';
import { cn } from '@/lib/utils';
import { Menu, Moon, Sun, X as XIcon } from 'lucide-react';
import { siBluesky, siDiscord, siGithub, siX, siYoutube } from 'simple-icons';

type Social = {
    url: string;
    icon: string;
};

type NavLink = {
    label: string;
    href: string;
};

export type DocsHeaderProps = {
    collectionTitle?: string | null;
    logo?: string;
    logoDark?: string;
    links?: NavLink[];
    socials?: Social[];
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

export function DocsHeader({ collectionTitle, logo, logoDark, links = [], socials = [] }: DocsHeaderProps) {
    const { isDark, toggle } = useDarkMode();
    const [mobileOpen, setMobileOpen] = React.useState(false);

    return (
        <header data-slot="docs-header" className="sticky top-0 z-30 w-full border-b border-border/40 bg-background/80 py-4 backdrop-blur-sm">
            <div className="mx-auto flex max-w-368 items-center gap-6 px-6 lg:px-12">
                <a href="/" className="inline-flex shrink-0 items-center">
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
                    <button onClick={toggle} className="text-muted-foreground hover:text-foreground" aria-label="Toggle dark mode">
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

                <button
                    onClick={() => setMobileOpen(true)}
                    className="flex-1 text-right text-muted-foreground hover:text-foreground lg:hidden"
                    aria-label="Open menu"
                >
                    <Menu className="ml-auto h-6 w-6" />
                </button>
            </div>

            {mobileOpen && (
                <>
                    <div className="fixed inset-0 z-40 bg-black/10 backdrop-blur-xs lg:hidden" onClick={() => setMobileOpen(false)} />
                    <aside className="fixed inset-y-0 right-0 z-50 flex w-72 flex-col border-l border-black/10 bg-background p-6 shadow-lg lg:hidden dark:border-white/10">
                        <div className="flex items-center justify-end gap-2">
                            <button onClick={toggle} className="text-muted-foreground hover:text-foreground" aria-label="Toggle dark mode">
                                {isDark ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
                            </button>
                            <button
                                onClick={() => setMobileOpen(false)}
                                className="text-muted-foreground hover:text-foreground"
                                aria-label="Close menu"
                            >
                                <XIcon className="h-5 w-5" />
                            </button>
                        </div>

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
                </>
            )}
        </header>
    );
}
