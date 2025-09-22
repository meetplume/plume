@php
    use App\Support\Icons;
    use App\Enums\Analytics;
    use Filament\Facades\Filament;
@endphp

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
    </x-slot>

    <x-slot:items class="mt-4">
        <x-dropdown.divider>
            @auth
                {{ str(auth()->user()->name)->limit(10) }}
            @else
                {{ __('Account') }}
            @endauth
        </x-dropdown.divider>
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
