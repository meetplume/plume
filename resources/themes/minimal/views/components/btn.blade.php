@php
    $isDisabled = $attributes->has('disabled') && $attributes->get('disabled');
@endphp

@props([
    'classes' => $attributes
        ->class([
            'bg-gray-200 hover:bg-gray-100 inline-block font-medium rounded-xl tracking-tight transition-colors dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white',
            'bg-primary-600! hover:bg-primary-500! text-white! dark:bg-primary-500! dark:hover:bg-primary-400! dark:text-white!' => $attributes->has('primary') && !$isDisabled,
            'bg-gray-100! text-gray-300! dark:bg-gray-700! dark:text-gray-500!' => $isDisabled,
            'px-[1.3rem] py-[.65rem]' => ! $attributes->has('size'),
            'px-6 py-3 text-lg' => 'md' === $attributes->get('size'),
            'px-[.65rem] py-[.35rem] text-sm rounded-md' => 'sm' === $attributes->get('size'),
            'px-[.65rem] py-[.35rem] text-xs rounded-sm' => 'xs' === $attributes->get('size'),
        ])
])

@if ($attributes->has('href'))
    <a {{ $classes }}>
        {{ $slot }}
    </a>
@else
    <button {{ $classes }} @if($isDisabled) disabled @endif>
        {{ $slot }}
    </button>
@endif
