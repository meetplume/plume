import { type PlumePageContext, MarkdownRenderer } from '@/components/plume/markdown-renderer';
import { type CollectionInfo, type NavItem, SidebarNav } from '@/components/plume/sidebar-nav';
import { Head } from '@inertiajs/react';

interface CollectionPageProps extends PlumePageContext {
    navigation?: NavItem[];
    collection?: CollectionInfo;
}

export default function Page(page: CollectionPageProps) {
    const hasNav = page.navigation && page.navigation.length > 0;

    return (
        <>
            <Head>
                {page.title && <title>{page.title}</title>}
                {page.description && <meta name="description" content={page.description} />}
            </Head>
            {hasNav ? (
                <div className="flex min-h-screen">
                    <SidebarNav navigation={page.navigation!} collection={page.collection} />
                    <main className="min-w-0 flex-1">
                        <article className="mx-auto prose max-w-3xl px-8 py-12 dark:prose-invert">
                            <MarkdownRenderer page={page} />
                        </article>
                    </main>
                </div>
            ) : (
                <article className="mx-auto prose max-w-3xl px-6 py-12 dark:prose-invert">
                    <MarkdownRenderer page={page} />
                </article>
            )}
        </>
    );
}
