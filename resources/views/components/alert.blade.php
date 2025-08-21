@props([
    'type',
    'icon',
    'heading',
    'class' => '',
])

@php
    $typeClass = match ($type){
        'info' => 'bg-blue-50 border-blue-200 text-blue-700',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-700',
        'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
        'danger' => 'bg-red-50 border-red-200 text-red-700',
        'success' => 'bg-green-50 border-green-200 text-green-700',
    };
    $icon ??= match($type){
        'info' => 'heroicon-o-information-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        'pending' => 'heroicon-o-clock',
        'danger' => 'heroicon-o-exclamation-circle',
        'success' => 'heroicon-o-check-circle',
    }
@endphp

<div class="{{ $typeClass }} {{ $class }} flex items-start gap-3 text-sm border px-4 py-3 rounded-lg relative">
    <x-icon name="{{ $icon }}" class="w-6 h-6 shrink-0"/>
    <div class="flex flex-col">
        @if(!empty($heading))
            <div class="font-bold">
                {{ $heading }}
            </div>
        @endif
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
