import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            hotFile: 'playground/public/hot',
        }),
        react({
            babel: mode === 'build' ? {
                plugins: ['babel-plugin-react-compiler'],
            } : undefined,
        }),
        tailwindcss(),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    build: {
        outDir: 'dist',
        emptyOutDir: true,
    },
}));
