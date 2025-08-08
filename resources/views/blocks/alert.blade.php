@php

    use App\Filament\CustomBlocks\CodeBlock;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;

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
        'warning' => 'border-yellow-400 bg-yellow-400/10 text-yellow-950 dark:bg-yellow-500/20 dark:text-yellow-100 prose-code:[p_&]:bg-yellow-600/10 prose-code:dark:[p_&]:bg-white/20',
        'danger' => 'border-red-400 bg-red-400/10 text-red-950 dark:bg-red-500/20 dark:text-red-100 prose-code:[p_&]:bg-red-600/10 prose-code:dark:[p_&]:bg-white/20',
        default => 'border-sky-400 bg-sky-400/10 text-sky-950 dark:bg-sky-500/20 dark:text-sky-100 prose-code:[p_&]:bg-sky-600/10 prose-code:dark:[p_&]:bg-white/20',
    };

@endphp

@if($preview)
    <div class="fi-fo-field mb-2">
        <div class="fi-fo-field-label-col">
            <div class="fi-fo-field-label-ctn ">
                <div class="fi-fo-field-label">
                    <span class="fi-fo-field-label-content">
                        {{ __('Preview') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif
    <div
        class=" alert-block alert-block-{{ $style }} mb-6 rounded-lg border-s-4 px-4 py-3 prose-p:my-0 {{ $classes }}">
        <div class="flex items-center gap-2 pb-2">
            <x-icon name="{{ $icon }}" class="size-6"/>
            @if(!empty($title))
                <p class="text-sm font-bold m-0!">
                    {{ $title }}
                </p>
            @endif
        </div>
        {!!
            RichContentRenderer::make($content)
                ->customBlocks([
                    CodeBlock::class,
                ])
                ->toHtml()
        !!}
    </div>
