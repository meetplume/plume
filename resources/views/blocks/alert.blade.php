@php

    if(!$preview && $config){
        $style = $config['style'];
        $title = $config['title'];
        $content = $config['content'];
    }
    else {
        $style = $get('style');
        $title = $get('title');
        $content = $get('content');
    }

    $icon = match($style){
        'tip' => 'heroicon-o-light-bulb',
        'warning' => 'heroicon-o-exclamation-triangle',
        'danger' => 'heroicon-o-fire',
        default => 'heroicon-o-information-circle',
    };

    $classes = match($style){
        'tip' => 'border-purple-400 bg-purple-400/10 text-purple-950 dark:bg-purple-500/20 dark:text-purple-100 prose-code:[p_&]:bg-purple-600/10 prose-code:dark:[p_&]:bg-white/20',
        'warning' => 'border-amber-400 bg-amber-400/10 text-amber-950 dark:bg-amber-500/20 dark:text-amber-100 prose-code:[p_&]:bg-amber-600/10 prose-code:dark:[p_&]:bg-white/20',
        'danger' => 'border-red-400 bg-red-400/10 text-red-950 dark:bg-red-500/20 dark:text-red-100 prose-code:[p_&]:bg-red-600/10 prose-code:dark:[p_&]:bg-white/20',
        default => 'border-blue-400 bg-blue-400/10 text-blue-950 dark:bg-blue-500/20 dark:text-blue-100 prose-code:[p_&]:bg-blue-600/10 prose-code:dark:[p_&]:bg-white/20',
    };

@endphp

@if($preview)
    <div class="fi-fo-field">
        <div class="fi-fo-field-label-col">
            <div class="fi-fo-field-label-ctn ">
                <div class="fi-fo-field-label">
                    <span class="fi-fo-field-label-content">
                        {{ __('Preview') }}
                    </span>
                </div>
            </div>
        </div>
        @endif
        <div
            class="alert-block alert-block-{{ $style }} mb-6 rounded-lg border-s-4 px-4 py-3 prose-p:my-0 {{ $classes }}">
            <div class="flex items-center gap-2 pb-2">
                <x-icon name="{{ $icon }}" class="size-6"/>
                @if(!empty($title))
                    <p class="text-sm font-bold">
                        {{ $title }}
                    </p>
                @endif
            </div>
            {!! $content !!}
        </div>
        @if($preview)
    </div>
@endif
