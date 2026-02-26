<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Plume Playground</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    </head>
    <body class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">Plume Playground</h1>
            <div class="flex gap-4 justify-center">
                <a href="/movies" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">Movies</a>
                <a href="/tv-shows" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">TV Shows</a>
                <a href="/laravel-zero" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">Laravel Zero</a>
                <a href="/laravel-zero-md" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">Laravel Zero MD</a>
                <a href="/plume/docs/introduction" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">Our Docs</a>
                <a href="/laravel-zero/docs/introduction" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">Laravel Zero Docs</a>
                <a href="/minglejs/docs/what-is-minglejs" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">MingleJS Docs</a>
            </div>
        </div>
    </body>
</html>
