<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Filament\CustomBlocks\CodeBlock;
use App\Filament\CustomBlocks\AlertBlock;
use App\Filament\RichEditorPlugins\IdPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

/**
 * @property int id
 * @property string title
 * @property string slug
 * @property ?array body
 * @property ?string excerpt
 * @property ?\Illuminate\Support\Carbon published_at
 * @property-read string $formattedContent
 * @property-read string $textContent
 * @property-read string $description
 */
class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, HasTranslations;

    protected $casts = [
        'published_at' => 'datetime',
        'body' => 'array',
    ];

    public array $translatable = [
        'title',
        'excerpt',
        'body',
    ];

    public function formattedContent(): Attribute
    {
        return Attribute::make(
            fn () => $this->body
                ? RichContentRenderer::make($this->body)
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDisk('public')
                    ->customBlocks([
                        CodeBlock::class,
                        AlertBlock::class,
                    ])
                    ->plugins([
                        IdPlugin::make(),
                    ])
                    ->toHtmlWithHeadingLinks()
                : ''
        )->shouldCache();
    }

    public function textContent(): Attribute
    {
        return Attribute::make(
            fn () => $this->body ? RichContentRenderer::make($this->body)->toText() : ''
        )->shouldCache();
    }

    public function description(): Attribute
    {
        return Attribute::make(
            fn () => str(filled($this->excerpt) ? $this->excerpt : ($this->textContent ?? ''))->limit(160)
        )->shouldCache();
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at');
    }
}
