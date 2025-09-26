@php
    use App\Enums\SiteSettings;
    use Filament\Facades\Filament;
    use Illuminate\Support\Facades\Storage;

    $logo = SiteSettings::SITE_LOGO->get();
    $siteName = SiteSettings::SITE_NAME->get();
@endphp

<div class="logo">
    <a
        wire:navigate
        href="{{ Filament::isServing() ? Filament::getHomeUrl() : route('home') }}"
        class="flex gap-3 items-center"
    >
        {{-- Logo --}}
        <div>
            @if($logo && Storage::disk('public')->exists($logo))
                <img
                    class="h-12 w-40 object-contain object-left"
                    src="{{ Storage::disk('public')->url($logo) }}"
                    alt="{{ $siteName }}"
                >
            @else
                <div class="monogram-wrapper bg-gray-950 dark:bg-gray-50 dark:text-black h-10 w-10 flex items-center justify-center rounded-lg text-2xl text-white font-semibold">
                    <div class="monogram">{{ str($siteName)->take(1)->upper() }}</div>
                </div>
            @endif
        </div>

        {{-- Site name --}}
        <span @class([
            'site-name font-semibold uppercase text-base tracking-widest text-gray-950 dark:text-gray-50 sr-only',
            'sm:not-sr-only' => SiteSettings::DISPLAY_SITE_NAME->get(),
        ])>
            {{ $siteName }}
        </span>
    </a>
</div>
