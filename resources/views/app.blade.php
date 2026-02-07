<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if (file_exists(public_path('hot')))
        @viteReactRefresh
        @vite(['resources/js/app.tsx'])
    @else
        @vite('resources/js/app.tsx', 'vendor/plume/dist')
    @endif
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
