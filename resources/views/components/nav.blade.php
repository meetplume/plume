@php
    use App\Support\Icons;
    use App\Enums\Analytics;
    use App\Enums\MainPages;
    use App\Enums\SiteSettings;
@endphp

<nav {{ $attributes->class('flex items-center gap-6 md:gap-8 justify-between') }}>

    <x-logo/>

    <div class="menu flex items-center gap-6 md:gap-8 font-normal text-sm">

        @if(count(LaravelLocalization::getSupportedLocales()) > 1)
            <x-language-switcher/>
        @endif

        @foreach(SiteSettings::MAIN_MENU->get() ?? [] as $menuItem)
            @php
                if(filled(data_get($menuItem, 'page')) && data_get($menuItem, 'page') !== 'custom'){
                    $url = url(data_get(SiteSettings::PERMALINKS->get(), data_get($menuItem, 'page')));
                    $name = MainPages::tryFrom(data_get($menuItem, 'page'))->getTitle();
                }
                else{
                    $url = url(data_get($menuItem, 'url'));
                    $name = data_get($menuItem, 'name');
                }
            @endphp

            <a
                data-pan="{{ Analytics::MAIN_MENU->value }}-{{ str($name)->slug()->toString() }}"
                href="{{ $url }}"
                target="{{ data_get($menuItem, 'open_in_new_tab') ? '_blank' : '' }}"
                @if(!data_get($menuItem, 'open_in_new_tab') && !str_contains($url,'#')) wire:navigate.hover @endif
                @class([
                    'transition-colors hover:text-primary-600 dark:hover:text-primary-500',
                    'text-primary-600 dark:text-primary-500' => str(request()->url())->remove('/' . app()->getLocale())->toString() === $url
                ])
            >
                {!! Icons::getHeroicon(
                    name: str(data_get($menuItem, 'icon'))->remove("o-"),
                    isOutlined: str(request()->url())->remove('/' . app()->getLocale())->toString() !== $url,
                    class: 'mx-auto size-6'
                ) !!}
                {{ $name }}
            </a>
        @endforeach

        <x-dropdown>
            <x-slot:btn
                data-pan="{{ Analytics::MAIN_MENU->value }}-more"
                class="transition-colors hover:text-primary-600 dark:hover:text-primary-500 cursor-pointer"
            >
                <div class="menu-icon" x-bind:class="{ 'active': open }">
                    <input class="menu-icon__checkbox" type="checkbox" name="more"/>
                    <div>
                        <span></span> <span></span>
                    </div>
                </div>
                {{ __('More') }}
            </x-slot>

            <x-slot:items class="mt-4">
                @foreach(SiteSettings::MAIN_MENU_MORE->get() ?? [] as $dropdownItem)

                    @if(data_get($dropdownItem, 'type') === 'divider')
                        <x-dropdown.divider>
                            {{ data_get($dropdownItem, 'data.label') }}
                        </x-dropdown.divider>
                    @else
                        @php
                            $defaultLanguage = SiteSettings::DEFAULT_LANGUAGE->get();
                            $currentLocale = app()->getLocale();
                            $localePrefix = ($currentLocale !== $defaultLanguage) ? "/{$currentLocale}" : "";

                            if(filled(data_get($dropdownItem, 'data.page')) && data_get($dropdownItem, 'data.page') !== 'custom'){
                                $path = data_get(SiteSettings::PERMALINKS->get(), data_get($dropdownItem, 'data.page'));
                                $url = url($localePrefix . '/' . $path);
                                $name = MainPages::tryFrom(data_get($dropdownItem, 'data.page'))->getTitle();
                            }
                            else{
                                $path = data_get($dropdownItem, 'data.url');
                                // Don't add locale prefix to external URLs or anchor links
                                $url = (str_starts_with($path, 'http') || str_starts_with($path, '#') || str_starts_with($path, 'mailto:'))
                                    ? url($path)
                                    : url($localePrefix . $path);
                                $name = data_get($dropdownItem, 'data.name');
                            }
                        @endphp
                        <x-dropdown.item
                            data-pan="{{ Analytics::DROPDOWN_MENU->value }}-{{ str($name)->slug()->toString() }}"
                            href="{{ $url }}"
                            target="{{ data_get($dropdownItem, 'data.open_in_new_tab') ? '_blank' : '' }}"
                        >
                            {!! svg(data_get($dropdownItem, 'data.icon'), 'size-4')->toHtml() !!}
                            {{ $name }}
                        </x-dropdown.item>
                    @endif
                @endforeach
            </x-slot>
        </x-dropdown>
    </div>
</nav>
