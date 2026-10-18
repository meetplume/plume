import { useTexts } from '@/lib/texts';
import { cn } from '@/lib/utils';
import { ArrowUpRight, Check, ChevronDown, Copy, FileDown, type LucideIcon, MessageCircle } from 'lucide-react';
import { DropdownMenu } from 'radix-ui';
import { Fragment, type ReactNode, useEffect, useRef, useState } from 'react';
import { siClaude, siMarkdown } from 'simple-icons';

export type PageAction = 'copy' | 'markdown' | 'chatgpt' | 'claude' | 'pdf';

/** The page props of the menu, given to every layout that shows it. */
export interface PageActionsContext {
    pageActions?: PageAction[];
    markdownUrl?: string;
}

type PageActionsProps = {
    /** The page's raw content, frontmatter included. */
    content: string;
    /** The address of the page's Markdown, served by Plume. */
    markdownUrl?: string;
    actions?: PageAction[];
    className?: string;
};

/**
 * The content without its frontmatter, as the page's `.md` address serves it
 * (`Frontmatter::strip()`), so that copying and viewing give the same text.
 */
export function stripFrontmatter(content: string): string {
    return content.replace(/^---\r?\n(?:[\s\S]*?\r?\n)??---[ \t]*(?:\r?\n|$)/, '').replace(/^[\r\n]+/, '');
}

/**
 * Whether the Markdown opens with its title (`# Title`, or a title underlined
 * with `===`), for the menu to sit beside it.
 */
function startsWithTitle(markdown: string): boolean {
    return /^#(?:[ \t]|\r?\n|$)/.test(markdown) || /^[^\r\n]+\r?\n=+[ \t]*(?:\r?\n|$)/.test(markdown);
}

function BrandIcon({ icon, className }: { icon: { path: string; title: string }; className?: string }) {
    return (
        <svg className={cn('size-4 fill-current', className)} viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d={icon.path} />
        </svg>
    );
}

/** The assistant's address, asked in the page's language to read the page's Markdown. */
function askUrl(service: 'chatgpt' | 'claude', markdownUrl: string, t: (key: string) => string): string {
    const prompt = t('Read :url and answer questions about the content.').replace(':url', new URL(markdownUrl, window.location.href).href);
    const base = service === 'chatgpt' ? 'https://chatgpt.com/' : 'https://claude.ai/new';

    return `${base}?q=${encodeURIComponent(prompt)}`;
}

const itemClassName =
    'flex cursor-pointer items-start gap-3 rounded-sm px-3 py-2 text-sm outline-hidden data-highlighted:bg-accent data-highlighted:text-accent-foreground';

function Item({ icon, title, description, external }: { icon: ReactNode; title: string; description: string; external?: boolean }) {
    return (
        <>
            <span className="mt-0.5 flex size-4 shrink-0 items-center justify-center text-muted-foreground">{icon}</span>
            <span className="flex flex-col gap-0.5">
                <span className="flex items-center gap-1 font-medium text-foreground">
                    {title}
                    {external && <ArrowUpRight className="size-3.5 text-muted-foreground" aria-hidden="true" />}
                </span>
                <span className="text-xs text-muted-foreground">{description}</span>
            </span>
        </>
    );
}

function LinkItem({ href, ...item }: { href: string; icon: ReactNode; title: string; description: string }) {
    return (
        <DropdownMenu.Item asChild className={itemClassName}>
            <a href={href} target="_blank" rel="noopener noreferrer">
                <Item {...item} external />
            </a>
        </DropdownMenu.Item>
    );
}

const iconClassName = 'size-4';

/**
 * The page menu, next to the page title (or above the page when it opens
 * with no title): copy the page's Markdown, open it, ask ChatGPT or Claude
 * about it, or print it as a PDF.
 */
export function PageActions({ content, markdownUrl, actions = [], className }: PageActionsProps) {
    const t = useTexts();
    const [copied, setCopied] = useState(false);
    const timeout = useRef<number | undefined>(undefined);

    useEffect(() => () => window.clearTimeout(timeout.current), []);

    const markdown = stripFrontmatter(content);
    const offers = (action: PageAction) => actions.includes(action) && (action === 'copy' || action === 'pdf' || markdownUrl !== undefined);
    const canCopy = offers('copy');
    const menu = actions.filter(offers);

    if (markdown.trim() === '' || menu.length === 0) {
        return null;
    }

    const copy = () => {
        navigator.clipboard
            ?.writeText(markdown)
            .then(() => {
                setCopied(true);
                window.clearTimeout(timeout.current);
                timeout.current = window.setTimeout(() => setCopied(false), 2000);
            })
            .catch(() => setCopied(false));
    };

    const CopyIcon: LucideIcon = copied ? Check : Copy;
    const copyLabel = copied ? t('Copied!') : t('Copy page');

    const groups: ReactNode[][] = [
        [
            canCopy && (
                <DropdownMenu.Item
                    key="copy"
                    className={itemClassName}
                    onSelect={(event) => {
                        event.preventDefault();
                        copy();
                    }}
                >
                    <Item icon={<CopyIcon className={iconClassName} />} title={copyLabel} description={t('Copy page as Markdown for LLMs')} />
                </DropdownMenu.Item>
            ),
            offers('markdown') && (
                <LinkItem
                    key="markdown"
                    href={markdownUrl!}
                    icon={<BrandIcon icon={siMarkdown} />}
                    title={t('View as Markdown')}
                    description={t('View this page as plain text')}
                />
            ),
        ],
        [
            offers('chatgpt') && (
                <LinkItem
                    key="chatgpt"
                    href={askUrl('chatgpt', markdownUrl!, t)}
                    icon={<MessageCircle className={iconClassName} />}
                    title={t('Open in ChatGPT')}
                    description={t('Ask ChatGPT about this page')}
                />
            ),
            offers('claude') && (
                <LinkItem
                    key="claude"
                    href={askUrl('claude', markdownUrl!, t)}
                    icon={<BrandIcon icon={siClaude} />}
                    title={t('Open in Claude')}
                    description={t('Ask Claude about this page')}
                />
            ),
        ],
        [
            offers('pdf') && (
                <DropdownMenu.Item key="pdf" className={itemClassName} onSelect={() => window.setTimeout(() => window.print())}>
                    <Item
                        icon={<FileDown className={iconClassName} />}
                        title={t('Export as PDF')}
                        description={t('Print this page or save it as a PDF')}
                    />
                </DropdownMenu.Item>
            ),
        ],
    ]
        .map((group) => group.filter(Boolean))
        .filter((group) => group.length > 0);

    const copyButton = canCopy && (
        <button
            type="button"
            onClick={copy}
            className="inline-flex h-8 items-center gap-1.5 px-2.5 text-sm font-medium text-muted-foreground transition-colors outline-none hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring"
        >
            <CopyIcon className="size-4 shrink-0" aria-hidden="true" />
            {copyLabel}
        </button>
    );

    return (
        <div
            data-slot="page-actions"
            className={cn('not-prose print:hidden', startsWithTitle(markdown) ? 'float-right mb-2 ml-4' : 'mb-6 flex justify-end', className)}
        >
            <div className="inline-flex items-stretch divide-x divide-border overflow-hidden rounded-md border border-border bg-background shadow-xs">
                {copyButton}
                <span role="status" className="sr-only">
                    {copied ? t('Copied!') : ''}
                </span>
                {(menu.length > 1 || !canCopy) && (
                    <DropdownMenu.Root modal={false}>
                        <DropdownMenu.Trigger
                            aria-label={canCopy ? t('More page actions') : undefined}
                            className="inline-flex h-8 items-center gap-1.5 px-1.5 text-sm font-medium text-muted-foreground transition-colors outline-none hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring data-[state=open]:bg-accent data-[state=open]:text-foreground [&[data-state=open]>svg]:rotate-180"
                        >
                            {!canCopy && <span className="pl-1">{t('Page')}</span>}
                            <ChevronDown className="size-4 transition-transform" aria-hidden="true" />
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Portal>
                            <DropdownMenu.Content
                                align="end"
                                sideOffset={6}
                                className="z-20 w-max max-w-[calc(100vw-2rem)] min-w-72 rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:animate-in data-[state=open]:fade-in-0 print:hidden"
                            >
                                {groups.map((group, index) => (
                                    <Fragment key={index}>
                                        {index > 0 && <DropdownMenu.Separator className="-mx-1 my-1 h-px bg-border" />}
                                        <DropdownMenu.Group>{group}</DropdownMenu.Group>
                                    </Fragment>
                                ))}
                            </DropdownMenu.Content>
                        </DropdownMenu.Portal>
                    </DropdownMenu.Root>
                )}
            </div>
        </div>
    );
}
