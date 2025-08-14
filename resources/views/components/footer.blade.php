@php
    use App\Enums\Analytics;
    use App\Enums\SiteSettings;
    use App\Support\Footer\FooterMenu;
@endphp

<div {{ $attributes->class('bg-gray-100 dark:bg-gray-800') }}>
    <footer class="container py-8 lg:max-w-(--breakpoint-md) *:[&_a]:hover:underline *:[&_a]:font-medium">
        <div class="flex flex-row justify-center items-center w-full">
            <div>
                <nav class="flex flex-col sm:flex-row gap-y-2 gap-x-8 place-items-center mx-auto w-full">
                    @foreach(FooterMenu::getFooterMenuItems() as $menuItem)
                        <a
                            data-pan="{{ Analytics::FOOTER_MENU->value }}-{{ str($menuItem->name)->slug()->toString() }}"
                            href="{{ $menuItem->url }}"
                            target="{{ $menuItem->open_in_new_tab ? '_blank' : '' }}"
                            @if(!$menuItem->open_in_new_tab && !str_contains($menuItem->url,'#')) wire:navigate.hover
                            @endif
                            class="text-center"
                        >
                            {{ $menuItem->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="mt-8 text-center">
            {!! SiteSettings::FOOTER_TEXT->get() !!}
        </div>

        <p class="mt-8 text-center text-gray-400">{{ str(SiteSettings::COPYRIGHT_TEXT->get())->replace('{year}', date('Y')) }}</p>
    </footer>
</div>
