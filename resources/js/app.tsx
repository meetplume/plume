import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { Customizer, type CustomizerInitialData } from './components/customizer/customizer';
import { applyTheme } from './lib/theme';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const plume = (props.initialPage.props as Record<string, unknown>).plume as CustomizerInitialData | undefined;

        if (plume?.theme) {
            applyTheme(plume.theme);
        }

        const root = createRoot(el);

        root.render(
            <StrictMode>
                <App {...props} />
                <Customizer initialData={plume} />
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});
