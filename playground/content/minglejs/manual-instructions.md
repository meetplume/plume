# Manual Instructions

In this page, we'll cover the manual changes we have to make on the project files to get Mingle working.

## File: `vite.config.js`

- We need to import `path`, and `vue` (if you choose to use Vue)
- We need to define the alias for the Mingle resources, since it's inside `vendor`, not `node_modules`.

```js{3-5,8-12,14-21}
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    resolve: {
        alias: {
            "@mingle": path.resolve(__dirname, "/vendor/ijpatricio/mingle/resources/js"),
        },
    },
    plugins: [
        react(),
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
            ],
            refresh: true,
        }),
    ],
});
```


## Layout files

On every layout where we want to use Mingle, we need to include the Blade directive `@mingles` so that Mingle can inject the necessary scripts.

On:

- `resources/views/layouts/guest.blade.php`
- `resources/views/layouts/app.blade.php`
- Or any other layout you might have

Add the Blade directive `@mingles` on the head of the document.

```html{8,10}
    (...)
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @mingles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
    (...)
```

::: tip 
If you're using Layout Components, e.g. `<x-app-layout>`, you can add the stack on the component itself.
:::


