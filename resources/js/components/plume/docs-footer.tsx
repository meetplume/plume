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
    return (
        <footer data-slot="docs-footer" className="mx-auto w-full max-w-full border-t border-border/40 py-8 lg:max-w-3xl">
            {(prev || next) && (
                <div className="flex items-center justify-between pb-6">
                    {prev ? (
                        <a href={prev.href} className="group flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                            <ChevronLeft className="size-4" />
                            <span>{prev.label}</span>
                        </a>
                    ) : (
                        <span />
                    )}
                    {next ? (
                        <a href={next.href} className="group flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                            <span>{next.label}</span>
                            <ChevronRight className="size-4" />
                        </a>
                    ) : (
                        <span />
                    )}
                </div>
            )}
            <div className="flex items-center justify-between text-sm text-muted-foreground [&_a]:font-medium [&_a]:text-foreground [&_a]:hover:underline">
                <div>{text && <span dangerouslySetInnerHTML={{ __html: text }} />}</div>
                <div className="text-muted-foreground">
                    Powered by{' '}
                    <a
                        href={`https://meetplume.com?ref=${typeof window !== 'undefined' ? window.location.origin : ''}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-muted-foreground"
                    >
                        Plume
                    </a>
                </div>
            </div>
        </footer>
    );
}
