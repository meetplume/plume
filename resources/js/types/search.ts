export type SearchRecord = {
    id: string;
    slug: string;
    title: string;
    description: string;
    headings: string[];
    body: string;
    href: string;
    group: string | null;
};

export type SearchHit = SearchRecord & {
    score: number;
    matchedTerms: string[];
};

export type SearchStatus = 'idle' | 'loading' | 'ready' | 'error';
