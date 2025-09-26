@php
    use App\Support\Icons;
    use App\Enums\Analytics;
@endphp

@if($menuItem->component)
    <x-dynamic-component :component="$menuItem->component" />
@else
    <a
        data-pan="{{ Analytics::MAIN_MENU->value }}-{{ str($menuItem->nameForAnalytics)->slug()->toString() }}"
        href="{{ $menuItem->url }}"
        target="{{ $menuItem->open_in_new_tab ? '_blank' : '' }}"
        @if(!$menuItem->open_in_new_tab && !str_contains($menuItem->url,'#')) wire:navigate.hover @endif
        @class([
            'transition-colors hover:text-primary-600 dark:hover:text-primary-500 text-base font-medium',
            'text-primary-600 dark:text-primary-500' => $menuItem->is_active
        ])
    >
        {{ $menuItem->name }}
    </a>
@endif
