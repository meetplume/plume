import { router } from '@inertiajs/react';
import { useEffect, useMemo } from 'react';

import type { DocumentationIndexProps } from '@/types/plume';

export default function DocumentationIndex({ collection, pages, navigation }: DocumentationIndexProps) {
    const firstSlug = useMemo(() => {
        if (navigation?.navigation && navigation.navigation.length > 0) {
            const firstPage = navigation.navigation[0].pages[0];
            if (firstPage) {
                return firstPage;
            }
        }

        return pages[0]?.slug;
    }, [pages, navigation]);

    useEffect(() => {
        if (firstSlug) {
            router.visit(`${collection.prefix}/${firstSlug}`, { replace: true });
        }
    }, [firstSlug, collection.prefix]);

    return (
        <div className="flex min-h-screen items-center justify-center bg-background">
            <p className="text-muted-foreground">Redirecting...</p>
        </div>
    );
}
