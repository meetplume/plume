import { usePage } from '@inertiajs/react';
import { useCallback } from 'react';

/**
 * The words of Plume's interface, translated by the server in the page's
 * locale (`InterfaceTexts`). A missing key falls back to the English one.
 */
export function useTexts(): (key: string) => string {
    const texts = (usePage().props as { texts?: Record<string, string> }).texts;

    return useCallback((key: string) => texts?.[key] ?? key, [texts]);
}
