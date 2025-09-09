@php
    use App\Support\Icons;
    use App\Enums\Analytics;
@endphp

<a
    data-pan="{{ Analytics::MAIN_MENU->value }}-{{ str($menuItem->nameForAnalytics)->slug()->toString() }}"
    href="{{ $menuItem->url }}"
    target="{{ $menuItem->open_in_new_tab ? '_blank' : '' }}"
    @if(!$menuItem->open_in_new_tab && !str_contains($menuItem->url,'#')) wire:navigate.hover @endif
    @class([
        'transition-colors hover:text-primary-600 dark:hover:text-primary-500',
        'text-primary-600 dark:text-primary-500' => $menuItem->is_active
    ])
>
    {!! Icons::getHeroicon(
        name: str($menuItem->icon)->remove("o-"),
        isOutlined: !$menuItem->is_active,
        class: 'mx-auto size-6'
    ) !!}
    {{ $menuItem->name }}
</a>
