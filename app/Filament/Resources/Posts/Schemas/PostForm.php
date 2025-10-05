<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Livewire\Component;
use Filament\Actions\Action;
use App\Support\SlugGenerator;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Flex;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Enums\Operation;
use Filament\Schemas\Components\Group;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Section;
use App\Filament\CustomBlocks\CodeBlock;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\RichEditor;
use App\Filament\CustomBlocks\AlertBlock;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Resources\Tags\Schemas\TagForm;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Categories\Schemas\CategoryForm;

class PostForm
{
    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([

                    // Main content
                    Group::make([
                        TextInput::make('title')
                            ->live(onBlur: true)
                            ->required()
                            ->afterStateUpdated(function (?string $state, Set $set, ?Model $record, $operation) {
                                if (in_array($operation, ['create', 'createOption'])) {
                                    $slug = SlugGenerator::unique(modelClass: Post::class, title: $state,
                                        ignoreRecord: $record);
                                    $set('slug', $slug);
                                }
                            }),

                        TextInput::make('slug')
                            ->partiallyRenderAfterStateUpdated()
                            ->helperText(function ($record, $operation) {
                                if ($operation === Operation::Create->value) {
                                    return new HtmlString('The slug is auto-generated from the title. You can change it if you want.');
                                }

                                $url = route('posts.show', ['post' => $record]);
                                return new HtmlString(Blade::render("<a href=\"{$url}\" target=\"_blank\"><x-heroicon-m-arrow-top-right-on-square class='size-3 mr-1 inline'/>{$url}</a>"));
                            }),

                        RichEditor::make('body')
                            ->json()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'code', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles', 'customBlocks'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                                ['undo', 'redo'],
                            ])
                            ->floatingToolbars([
                                'paragraph' => [
                                    'bold', 'italic', 'underline', 'strike', 'code', 'h2', 'h3', 'link'
                                ],
                                'heading' => [
                                    'h2', 'h3',
                                ],
                                'table' => [
                                    'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                                    'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                                    'tableMergeCells', 'tableSplitCell',
                                    'tableToggleHeaderRow',
                                    'tableDelete',
                                ],
                            ])
                            ->customBlocks([
                                CodeBlock::class,
                                AlertBlock::class,
                            ])
                            ->hiddenLabel(),
                    ]),

                    // Sidebar
                    Group::make([

                        Section::make('Publish')
                            ->hiddenOn(Operation::Create)
                            ->schema([
                            TextEntry::make('status')
                                ->badge()
                                ->label(__('Status'))
                                ->hiddenLabel()
                                ->beforeContent(__('Status: '))
                                ->state(fn(Post $record) => $record->published_at ? __('published') : __('draft'))
                                ->color(fn(Post $record) => $record->published_at ? 'success' : 'gray'),
                            DateTimePicker::make('published_at')->label('Publish on')->live(),
                            Action::make('save')
                                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                                ->visible(fn(Get $get) => $get('published_at') !== null)
                                ->submit('save'),
                            Action::make('publishNow')
                                ->label(__('Publish now'))
                                ->visible(fn(Get $get) => $get('published_at') === null)
                                ->requiresConfirmation()
                                ->modalHeading(__("Publish this post?"))
                                ->modalDescription(__("Are you sure you'd like to publish this post?"))
                                ->action(function (EditRecord $livewire, Set $set) {
                                    $set('published_at', now()->format('Y-m-d H:i:s'));
                                    $livewire->save();
                                }),
                            Action::make('unpublish')
                                ->label(__('Unpublish'))
                                ->color('danger')
                                ->link()
                                ->visible(fn(Post $record) => isset($record->published_at))
                                ->requiresConfirmation()
                                ->modalHeading(__("Unpublish this post?"))
                                ->modalDescription(__("Are you sure you'd like to unpublish this post?"))
                                ->action(function (Post $record, Set $set, Get $get) {
                                    $set('published_at', null);
                                    $record->update(['published_at' => null]);
                                })
                        ])->compact(),

                        Section::make('Post Settings')->schema([
                            FileUpload::make('image')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->imageEditor(),

                            Select::make('author_id')
                                ->relationship('author', 'name'),

                            Select::make('categories')
                                ->multiple()
                                ->relationship('categories', 'name')
                                ->createOptionForm(fn() => CategoryForm::getForm())
                                ->createOptionModalHeading('Add a new category')
                                ->preload()
                                ->allowHtml(),

                            Select::make('tags')
                                ->multiple()
                                ->relationship('tags', 'name')
                                ->createOptionForm(fn() => TagForm::getForm())
                                ->createOptionModalHeading('Add a new tag')
                                ->preload(),

                            Textarea::make('excerpt')->rows(4),

                        ])->compact(),

                    ])->grow(false)->extraAttributes(['style' => 'max-width:300px;']),

                ])->from('md')

            ])->columns(1);
    }
}
