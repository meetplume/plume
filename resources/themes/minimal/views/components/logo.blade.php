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
                <style>
                    .monogram {
                        --tw-gradient-position: 135deg in oklab;
                        background-image: linear-gradient(var(--tw-gradient-stops));
                        --tw-gradient-from: var(--color-primary-600);
                        --tw-gradient-to: color-mix(in oklab, var(--color-primary-300) 90%, transparent);
                        --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
                        background-clip: text;
                        color: transparent;
                    }
                </style>
                <div class="monogram-wrapper h-10 w-10 flex items-center justify-center text-4xl font-bold">
                    <div class="monogram">{{ str($siteName)->take(1)->upper() }}</div>
                </div>
            @endif
        </div>

        {{-- Site name --}}
        <span @class([
            'site-name font-semibold uppercase text-base tracking-widest text-gray-50 sr-only',
            'sm:not-sr-only' => SiteSettings::DISPLAY_SITE_NAME->get(),
        ])>
            {{ $siteName }}
        </span>
    </a>
</div>
