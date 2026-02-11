export interface CollectionMeta {
    prefix: string;
    title: string;
    type: string;
}

export interface Page {
    slug: string;
    title: string;
    description: string;
    rawContent: string;
}

export interface NavigationSection {
    section: string;
    pages: string[];
}

export interface CollectionNavigation {
    title?: string;
    navigation?: NavigationSection[];
}

export interface DocumentationShowProps {
    collection: CollectionMeta;
    pages: Page[];
    currentSlug: string;
    navigation: CollectionNavigation | null;
}

export interface DocumentationIndexProps {
    collection: CollectionMeta;
    pages: Page[];
    navigation: CollectionNavigation | null;
}

export interface TocItem {
    id: string;
    text: string;
    level: number;
}

export interface SidebarSection {
    title: string;
    pages: Page[];
}
