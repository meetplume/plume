@php use Meetplume\Plume\ThemeConfig; @endphp
<html lang="{{ str_replace('_', '-', $page['props']['locale'] ?? app()->getLocale()) }}" @if(app()->bound(ThemeConfig::class) && app(ThemeConfig::class)->defaultDark()) class="dark" @endif>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @plumeAssets
    @plumeInertiaHead
</head>
<body class="text-foreground bg-background">
@plumeInertia
</body>
</html>
