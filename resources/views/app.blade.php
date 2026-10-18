@php use Meetplume\Plume\Plume;use Meetplume\Plume\ThemeConfig; @endphp
@php($appearanceKey = app(Plume::class)->getConfiguration()?->getAppearanceKey() ?? 'theme')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $page['props']['locale'] ?? app()->getLocale()) }}" data-appearance-key="{{ $appearanceKey }}" @if(app()->bound(ThemeConfig::class) && app(ThemeConfig::class)->defaultDark()) class="dark" @endif>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Applies the stored light/dark choice before the first paint. --}}
    <script>
        (function () {
            var root = document.documentElement;
            var stored = null;

            try {
                stored = localStorage.getItem(root.dataset.appearanceKey);
            } catch (e) {}

            if (stored === 'dark' || stored === 'light') {
                root.classList.toggle('dark', stored === 'dark');
            } else if (!root.classList.contains('dark')) {
                root.classList.toggle('dark', window.matchMedia('(prefers-color-scheme: dark)').matches);
            }
        })();
    </script>
    @plumeAssets
    @include('plume::head')
    @plumeInertiaHead
</head>
<body class="text-foreground bg-background">
@plumeInertia
</body>
</html>
