@php
    use App\Models\Page;
    use App\Support\Icons;
    use App\Enums\Analytics;
    use App\Enums\MainPages;
    use App\Enums\SiteSettings;
    use Filament\Facades\Filament;
@endphp

<nav {{ $attributes->class('flex items-center gap-6 md:gap-8 justify-between') }}>

    <x-logo/>

    <div class="menu flex items-center gap-6 md:gap-8 font-normal text-sm">

        @if(SiteSettings::DARK_MODE->get() === 'switcher')
            <x-theme-switcher />
        @endif

        @if(count(LaravelLocalization::getSupportedLocales()) > 1)
            <x-language-switcher/>
        @endif

        @foreach(SiteSettings::MAIN_MENU->get() ?? [] as $menuItem)
            @php
                $pageKey = data_get($menuItem, 'page');
                $url = url('/'); // safe default
                $name = '';
                $nameForAnalytics = '';

                if (filled($pageKey) && $pageKey !== 'custom') {
                    if (str_starts_with($pageKey, 'page:')) {
                        $pageId = (int) str(data_get($menuItem, 'page'))->after('page:')->toString();
                        $pageModel = Page::query()->find($pageId);
                        if ($pageModel) {
                            $url = route('pages.show', ['page' => $pageModel]);
                            $name = $pageModel->title;
                            $nameForAnalytics = $pageModel->getTranslation('title', SiteSettings::DEFAULT_LANGUAGE->get());
                        } else {
                            $url = url('/');
                            $name = 'Page';
                            $nameForAnalytics = $name;
                        }
                    } else {
                        $permalink = data_get(SiteSettings::PERMALINKS->get(), $pageKey);
                        // Only call url() if we actually have a path
                        $url = filled($permalink) ? url($permalink) : url('/');
                        $name = MainPages::tryFrom($pageKey)?->getTitle() ?? '';
                        $nameForAnalytics = MainPages::tryFrom($pageKey)?->value ?? '';
                    }
                } else {
                    $raw = data_get($menuItem, 'url');
                    // Use external/anchor as-is; otherwise prefix with base url
                    if (is_string($raw) && Str::startsWith($raw, ['http', '#', 'mailto:'])) {
                        $url = $raw;
                    } else {
                        $url = filled($raw) ? url($raw) : url('/');
                    }
                    $name = data_get($menuItem, 'name');
                    $nameForAnalytics = $name;
                }
            @endphp

            <a
                data-pan="{{ Analytics::MAIN_MENU->value }}-{{ str($nameForAnalytics)->slug()->toString() }}"
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
                data-pan="{{ Analytics::MAIN_MENU->value }}-account"
                class="transition-colors hover:text-primary-600 dark:hover:text-primary-500 cursor-pointer"
            >
                {!! Icons::getHeroicon(
                    name: 'user',
                    isOutlined: true,
                    class: 'mx-auto size-6'
                ) !!}
                @auth
                    {{ str(auth()->user()->name)->limit(10) }}
                @else
                    {{ __('Account') }}
                @endauth
            </x-slot>

            <x-slot:items class="mt-4">
                @auth
                    <form method="POST" action="{{ Filament::getPanel('user')->getLogoutUrl() }}" class="inline">
                        @csrf
                        <x-dropdown.item
                            data-pan="{{ Analytics::MAIN_MENU->value }}-logout"
                            type="submit"
                        >
                            {!! svg('heroicon-c-arrow-right-start-on-rectangle', 'size-4')->toHtml() !!}
                            {{ __("Log out") }}
                        </x-dropdown.item>
                    </form>
                @else
                    <x-dropdown.item
                        data-pan="{{ Analytics::MAIN_MENU->value }}-login"
                        href="{{ Filament::getPanel('user')->getLoginUrl() }}"
                    >
                        {!! svg('heroicon-c-arrow-right-end-on-rectangle', 'size-4')->toHtml() !!}
                        {{ __('Log in') }}
                    </x-dropdown.item>
                    <x-dropdown.item
                        data-pan="{{ Analytics::MAIN_MENU->value }}-register"
                        href="{{ Filament::getPanel('user')->getRegistrationUrl() }}"
                    >
                        {!! svg('heroicon-c-user-plus', 'size-4')->toHtml() !!}
                        {{ __('Register') }}
                    </x-dropdown.item>
                @endauth
            </x-slot:items>
        </x-dropdown>

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
                                $pageKey = data_get($dropdownItem, 'data.page');
                                if (str_starts_with($pageKey, 'page:')) {
                                    $pageId = (int) str($pageKey)->after('page:')->toString();
                                    $pageModel = Page::find($pageId);
                                    if ($pageModel) {
                                        $url = route('pages.show', ['page' => $pageModel]);
                                        $name = $pageModel->title;
                                    } else {
                                        $path = '';
                                        $url = url($localePrefix);
                                        $name = 'Page';
                                    }
                                } else {
                                    $path = data_get(SiteSettings::PERMALINKS->get(), $pageKey);
                                    $url = url($localePrefix . '/' . $path);
                                    $name = MainPages::tryFrom($pageKey)->getTitle();
                                }
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
