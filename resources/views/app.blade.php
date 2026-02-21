@php use Meetplume\Plume\ThemeConfig; @endphp
<html @if(app()->bound(ThemeConfig::class) && app(ThemeConfig::class)->defaultDark()) class="dark" @endif>
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
<body class="text-foreground bg-background">
@inertia
</body>
</html>
