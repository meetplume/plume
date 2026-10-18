import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { lazy, StrictMode, Suspense } from 'react';
import { createRoot } from 'react-dom/client';
import type { CustomizerInitialData } from './components/customizer/customizer';
import { applyTheme } from './lib/theme';

const LazyCustomizer = lazy(() => import('./components/customizer/customizer').then((m) => ({ default: m.Customizer })));

// The site name set by `Plume::configure()->name()`, read from the first page.
let siteName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Paper is white: print in light mode, whatever the screen shows. A theme
// sets its colors inline on <html>, so they switch along with the class.
function showDarkMode(dark: boolean) {
    document.documentElement.classList.toggle('dark', dark);

    for (const [prop, value] of Object.entries(window.__plumeTheme?.[dark ? 'dark' : 'light'] ?? {})) {
        document.documentElement.style.setProperty(prop, value);
    }
}

let printedFromDark = false;

window.addEventListener('beforeprint', () => {
    printedFromDark = document.documentElement.classList.contains('dark');
    showDarkMode(false);
});
window.addEventListener('afterprint', () => showDarkMode(printedFromDark));

createInertiaApp({
    title: (title) => (title ? `${title} - ${siteName}` : siteName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const initialProps = props.initialPage.props as Record<string, unknown>;
        const plume = initialProps.plume as CustomizerInitialData | undefined;
        const site = initialProps.site as { name?: string } | undefined;

        if (site?.name) {
            siteName = site.name;
        }

        if (plume?.theme) {
            applyTheme(plume.theme);
        }

        const root = createRoot(el);

        root.render(
            <StrictMode>
                <App {...props} />
                {plume?.customizer?.enabled && (
                    <Suspense>
                        <LazyCustomizer initialData={plume} />
                    </Suspense>
                )}
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});
