import * as React from 'react';

import { useDarkMode } from '@/hooks/use-dark-mode';
import { cn } from '@/lib/utils';
import { Moon, Sun } from 'lucide-react';
import { siBluesky, siDiscord, siGithub, siX, siYoutube } from 'simple-icons';

type Social = {
    url: string;
    icon: string;
};

type NavLink = {
    label: string;
    href: string;
};

export type Header1Props = React.ComponentProps<'header'> & {
    logo: string;
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

export function Header1({ className, logo, logoDark, links = [], socials = [], ...props }: Header1Props) {
    const { isDark, toggle } = useDarkMode();

    return (
        <header data-slot="header1" className={cn('w-full py-4', className)} {...props}>
            <div className="container mx-auto flex items-center gap-6 px-6">
                <a href="/" className="inline-flex shrink-0 items-center">
                    {logoDark ? (
                        <>
                            <img className="h-8 dark:hidden" loading="lazy" src={logo} alt="Logo" />
                            <img className="hidden h-8 dark:block" loading="lazy" src={logoDark} alt="Logo" />
                        </>
                    ) : (
                        <img className="h-8" loading="lazy" src={logo} alt="Logo" />
                    )}
                </a>

                <div className="flex flex-1 items-center justify-end gap-6">
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
            </div>
        </header>
    );
}
