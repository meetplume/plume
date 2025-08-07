<?php

namespace App\Filament\CustomBlocks;

use Blade;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class AlertBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'alert';
    }

    public static function getLabel(): string
    {
        return 'Alert Block';
    }

    /**
     * @throws \Exception
     */
    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the alert block')
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                Select::make('style')
                    ->live()
                    ->default('info')
                    ->formatStateUsing(fn($state) => empty($state) ? 'info' : $state)
                    ->options([
                        'info' => Blade::render('<div class="flex gap-1 items-center px-2 py-1 rounded-md text-sm font-bold bg-blue-400/10 text-blue-950 dark:bg-blue-500/20 dark:text-blue-100"><x-icon name="heroicon-o-information-circle" class="size-6"></x-icon> <span>Info</span></div>'),
                        'tip' => Blade::render('<div class="flex gap-1 items-center px-2 py-1 rounded-md text-sm font-bold bg-purple-400/10 text-purple-950 dark:bg-purple-500/20 dark:text-purple-100"><x-icon name="heroicon-o-light-bulb" class="size-6"></x-icon> <span>Tip</span></div>'),
                        'warning' => Blade::render('<div class="flex gap-1 items-center px-2 py-1 rounded-md text-sm font-bold bg-amber-400/10 text-amber-950 dark:bg-amber-500/20 dark:text-amber-100"><x-icon name="heroicon-o-exclamation-triangle" class="size-6"></x-icon> <span>Warning</span></div>'),
                        'danger' => Blade::render('<div class="flex gap-1 items-center px-2 py-1 rounded-md text-sm font-bold bg-red-400/10 text-red-950 dark:bg-red-500/20 dark:text-red-100"><x-icon name="heroicon-o-fire" class="size-6"></x-icon> <span>Danger</span></div>'),
                    ])
                    ->native(false)
                    ->allowHtml(),

                TextInput::make('title')
                    ->formatStateUsing(fn($state) => empty($state) ? 'Your title here' : $state)
                    ->live(),

                RichEditor::make('content')
                    ->formatStateUsing(fn($state) => empty($state) || $state === '<p></p>' ? '<p>Lorem ipsum dolor sit amet, consectetur <code>adipiscing</code> elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.</p>' : $state)
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                        ['code', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->floatingToolbars([
                        'paragraph' => [
                            'bold', 'italic', 'underline', 'strike', 'code', 'link'
                        ],
                    ])
                    ->live(),

                View::make('blocks.alert')->viewData(['preview' => true]),

            ]);
    }

    public static function getPreviewLabel(array $config): string
    {
        return ucfirst($config['style']) . " alert block";
    }

    /**
     * @throws \Throwable
     */
    public static function toPreviewHtml(array $config): string
    {
        return view('blocks.alert', [
            'config' => $config,
            'preview' => false,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('blocks.alert', [
            'config' => $config,
            'preview' => false,
        ])->render();
    }

}
