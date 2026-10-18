import { useTexts } from '@/lib/texts';
import { cn } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface PrevNextLink {
    label: string;
    href: string;
}

export type DocsFooterProps = {
    text?: string;
    prev?: PrevNextLink | null;
    next?: PrevNextLink | null;
};

export function DocsFooter({ text, prev, next }: DocsFooterProps) {
    const t = useTexts();
    const site = usePage().props.site as { poweredBy?: boolean } | undefined;

    return (
        <footer data-slot="docs-footer" className="mx-auto w-full max-w-full border-t border-border/40 py-8 lg:max-w-3xl">
            {(prev || next) && (
                <nav className="grid gap-4 pb-8 sm:grid-cols-2">
                    {prev && <PrevNextCard link={prev} direction="prev" />}
                    {next && <PrevNextCard link={next} direction="next" />}
                </nav>
            )}
            <div className="flex items-center justify-between text-sm text-muted-foreground [&_a]:font-medium [&_a]:text-foreground [&_a]:hover:underline">
                <div>{text && <span dangerouslySetInnerHTML={{ __html: text }} />}</div>
                {site?.poweredBy !== false && (
                    <div className="text-muted-foreground">
                        {t('Powered by')}{' '}
                        <a
                            href={`https://meetplume.com?ref=${typeof window !== 'undefined' ? window.location.origin : ''}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-muted-foreground"
                        >
                            Plume
                        </a>
                    </div>
                )}
            </div>
        </footer>
    );
}

/** An Inertia visit, as in the sidebar: no full reload of the page. */
function PrevNextCard({ link, direction }: { link: PrevNextLink; direction: 'prev' | 'next' }) {
    const t = useTexts();
    const isNext = direction === 'next';
    const Chevron = isNext ? ChevronRight : ChevronLeft;

    return (
        <Link
            href={link.href}
            rel={direction}
            className={cn(
                'group flex items-center gap-3 rounded-xl border border-border px-4 py-3.5 transition-colors hover:border-foreground/20 hover:bg-muted/50',
                isNext && 'flex-row-reverse text-right sm:col-start-2',
            )}
        >
            <Chevron className="size-4 shrink-0 text-muted-foreground transition-colors group-hover:text-foreground" />
            <span className="flex min-w-0 flex-col gap-0.5">
                <span className="text-sm text-muted-foreground">{t(isNext ? 'Next' : 'Previous')}</span>
                <span className="font-medium text-foreground">{link.label}</span>
            </span>
        </Link>
    );
}
