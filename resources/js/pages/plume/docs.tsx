import { type SectionProps, Section } from '@/components/blocks/section';
import { type DocsFooterProps, DocsFooter } from '@/components/plume/docs-footer';
import { type DocsHeaderProps, DocsHeader } from '@/components/plume/docs-header';
import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { type NavItem, SidebarNav } from '@/components/plume/sidebar-nav';
import { TableOfContents } from '@/components/plume/table-of-contents';
import { Head } from '@inertiajs/react';

interface TabItem {
    key: string;
    label: string;
    icon: string | null;
    href: string;
    active: boolean;
}

interface VersionItem {
    key: string;
    href: string;
    active: boolean;
    default: boolean;
}

interface LanguageItem {
    code: string;
    name: string;
    href: string;
    active: boolean;
}

interface PrevNextLink {
    label: string;
    href: string;
}

interface DocsPageProps extends PlumePageContext {
    layout: 'docs';
    navigation: NavItem[];
    sections?: SectionProps[];
    header?: DocsHeaderProps;
    footer?: DocsFooterProps;
    tabs?: TabItem[];
    activeTab?: string;
    versions?: VersionItem[];
    activeVersion?: string;
    languages?: LanguageItem[];
    activeLanguage?: string;
    prev?: PrevNextLink | null;
    next?: PrevNextLink | null;
    site?: { name: string; logo: string | null; logoDark: string | null };
    vault?: { prefix: string };
}

export default function DocsLayout(page: DocsPageProps) {
    const hasSections = page.sections && page.sections.length > 0;

    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            <DocsHeader {...page.header} collectionTitle={page.site?.name} tabs={page.tabs} versions={page.versions} languages={page.languages} />
            <div className="mx-auto flex max-w-368 gap-8 px-6 lg:px-8">
                <SidebarNav navigation={page.navigation} />
                <main className="min-w-0 flex-1">
                    {hasSections && page.sections!.map((section, index) => <Section key={index} {...section} />)}
                    <article className="mx-auto prose max-w-full pt-8 pb-14 lg:max-w-3xl dark:prose-invert">
                        <MarkdownRenderer page={page} />
                    </article>
                    <DocsFooter {...page.footer} prev={page.prev} next={page.next} />
                </main>
                <div className="sticky top-[calc(var(--spacing)*16+1px)] h-[calc(100vh-var(--spacing)*16-5px)] w-64 shrink-0 overflow-y-auto pb-8 text-sm max-xl:hidden">
                    <TableOfContents />
                </div>
            </div>
        </>
    );
}
