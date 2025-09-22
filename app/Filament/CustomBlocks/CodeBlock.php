<?php

namespace App\Filament\CustomBlocks;

use Phiki\Grammar\Grammar;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use App\Services\CodeThemeService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CodeEditor;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class CodeBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'code';
    }

    public static function getLabel(): string
    {
        return 'Code Block';
    }

    /**
     * @throws \Exception
     */
    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the code block')
            ->modalWidth(Width::TwoExtraLarge)
            ->closeModalByClickingAway(false)
            ->schema([
                CodeEditor::make('code')
                    ->language(fn(Get $get) => Language::tryFrom($get('language') ?? Language::Php->value)),

                Select::make('language')
                    ->live()
                    ->default(Language::Php->value)
                    ->formatStateUsing(fn($state) => empty($state) ? Language::Php->value : $state)
                    ->options(collect(Language::cases())->mapWithKeys(fn(Language $language) => [
                        $language->value => $language->name,
                    ])),
            ]);
    }

    public static function getPreviewLabel(array $config): string
    {
        return "{$config['language']} code block";
    }

    public static function toPreviewHtml(array $config): string
    {
        return CodeThemeService::codeToHtml(
            code: $config['code'] ?? '',
            grammar: Grammar::tryFrom($config['language'] ?? Grammar::Php),
            theme: CodeThemeService::getCodeTheme(),
        );
    }

    public static function toHtml(array $config, array $data): string
    {
        return CodeThemeService::codeToHtml(
            code: $config['code'] ?? '',
            grammar: Grammar::tryFrom($config['language'] ?? Grammar::Php),
            theme: CodeThemeService::getCodeTheme(),
        );
    }
}
