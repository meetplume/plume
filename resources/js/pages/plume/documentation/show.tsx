import { useMemo } from 'react';

import { DocumentationLayout } from '@/components/plume/documentation-layout';
import { extractTocItems } from '@/components/plume/table-of-contents';
import type { DocumentationShowProps, Page, SidebarSection } from '@/types/plume';

export default function DocumentationShow({ collection, pages, currentSlug, navigation }: DocumentationShowProps) {
    const orderedPages = useMemo(() => {
        if (!navigation?.navigation) {
            return pages;
        }

        const order = navigation.navigation.flatMap((section) => section.pages);
        const ordered: Page[] = [];
        const pageMap = new Map(pages.map((p) => [p.slug, p]));

        for (const slug of order) {
            const page = pageMap.get(slug);
            if (page) {
                ordered.push(page);
                pageMap.delete(slug);
            }
        }

        for (const page of pageMap.values()) {
            ordered.push(page);
        }

        return ordered;
    }, [pages, navigation]);

    const sections = useMemo((): SidebarSection[] => {
        if (navigation?.navigation) {
            const pageMap = new Map(pages.map((p) => [p.slug, p]));
            const sections: SidebarSection[] = navigation.navigation.map((nav) => ({
                title: nav.section,
                pages: nav.pages.map((slug) => pageMap.get(slug)).filter((p): p is Page => p !== undefined),
            }));

            const categorized = new Set(navigation.navigation.flatMap((n) => n.pages));
            const uncategorized = pages.filter((p) => !categorized.has(p.slug));
            if (uncategorized.length > 0) {
                sections.push({ title: 'Other', pages: uncategorized });
            }

            return sections;
        }

        return [{ title: 'Pages', pages }];
    }, [pages, navigation]);

    const currentPage = useMemo(() => {
        return pages.find((p) => p.slug === currentSlug) || pages[0];
    }, [pages, currentSlug]);

    const currentIndex = orderedPages.findIndex((p) => p.slug === currentSlug);
    const previousPage = currentIndex > 0 ? orderedPages[currentIndex - 1] : null;
    const nextPage = currentIndex < orderedPages.length - 1 ? orderedPages[currentIndex + 1] : null;

    const tocItems = useMemo(() => extractTocItems(currentPage.rawContent), [currentPage.rawContent]);

    return (
        <DocumentationLayout
            collectionTitle={collection.title}
            prefix={collection.prefix}
            sections={sections}
            allPages={orderedPages}
            currentPage={currentPage}
            currentSlug={currentSlug}
            previousPage={previousPage}
            nextPage={nextPage}
            tocItems={tocItems}
            codeThemeLight={collection.codeThemeLight}
            codeThemeDark={collection.codeThemeDark}
        />
    );
}
