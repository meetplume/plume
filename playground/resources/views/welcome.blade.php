<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>App name</title>

        @if (file_exists(public_path('hot')))
            @viteReactRefresh
            @vite(['resources/js/app.tsx'])
        @else
            @vite('resources/js/app.tsx', 'vendor/plume/dist')
        @endif

    </head>
    <body>
    <div id="app"></div>
    </body>
</html>
