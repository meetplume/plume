# Auto Import on Build

When we create a build (`npm run build`), we need to specify the files we want to process. We do that in the `vite.config.js` file, on the key `plugins.laravel.input`.

Sometimes, we may have plenty of files to process, and it can be a hassle to keep track of them.  To make it easier, we can use the helper:

```js{6-9,30}
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

import findMingles from './vendor/ijpatricio/mingle/resources/js/autoImport.js'
const mingles = findMingles('resources/js')
// Optional: Output the mingles to the console, for a visual check
console.log('Auto-importing mingles:', mingles)

export default defineConfig({
    resolve: {
        alias: {
            "@mingle": path.resolve(__dirname, "/vendor/ijpatricio/mingle/resources/js"),
        },
    },
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...mingles,
            ],
            refresh: true,
        }),
    ],
});
```
