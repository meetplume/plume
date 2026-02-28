export type DocsFooterProps = {
    text?: string;
};

export function DocsFooter({ text }: DocsFooterProps) {
    return (
        <footer
            data-slot="docs-footer"
            className="mx-auto flex w-full max-w-full items-center justify-between border-t border-border/40 py-8 text-center text-sm text-muted-foreground lg:max-w-3xl [&_a]:font-medium [&_a]:text-foreground [&_a]:hover:underline"
        >
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
        </footer>
    );
}
