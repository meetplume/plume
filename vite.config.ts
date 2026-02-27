import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            hotFile: 'playground/public/hot',
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    base: command === 'build' ? '/vendor/plume/dist/' : undefined,
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    react: ['react', 'react-dom', 'react/jsx-runtime'],
                    markdown: [
                        'unified',
                        'remark-parse',
                        'remark-gfm',
                        'remark-frontmatter',
                        'remark-directive',
                        'remark-github-admonitions-to-directives',
                        'remark-rehype',
                        'rehype-raw',
                        'rehype-slug',
                        'rehype-external-links',
                        'rehype-stringify',
                    ],
                },
            },
        },
    },
}));
