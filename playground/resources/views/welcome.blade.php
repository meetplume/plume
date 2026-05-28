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
            @php
                $links = [
                    ['url' => '/plume-docs-contributing', 'title' => 'Plume Docs Contributing'],
                    ['url' => '/laravel-zero', 'title' => 'Laravel Zero'],
                    ['url' => '/minglejs', 'title' => 'MingleJS'],
                ];
            @endphp
            <div class="flex gap-4 justify-center">
                @foreach ($links as $link)
                    <a href="{{ $link['url'] }}" target="_blank" class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">
                        {{ $link['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </body>
</html>
