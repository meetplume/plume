import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { glob } from 'glob';

// Dynamically find all theme assets
const themeAssets = glob.sync('resources/themes/**/*.{css,js}');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                ...themeAssets,
            ],
            refresh: [
                'resources/themes/**', // Watch theme changes
            ],
        }),
        tailwindcss(),
    ],
});
