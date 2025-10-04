import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { glob } from 'glob';
import path from 'path'
import { watchAndRun } from 'vite-plugin-watch-and-run'

// Dynamically find all theme assets
const themeAssets = glob.sync('resources/themes/**/*.{css,js}');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/prezet.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                ...themeAssets,
            ],
            refresh: [
                ...refreshPaths,
                'resources/themes/**', // Watch theme changes
            ],
        }),
        tailwindcss(),
        watchAndRun([
            {
                name: 'prezet:index',
                watch: path.resolve('prezet/**/*.(md|jpg|png|webp)'),
                run: 'php artisan prezet:index',
                delay: 1000,
                // watchKind: ['add', 'change', 'unlink'], // (default)
            },
        ]),
    ],
})
