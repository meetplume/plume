<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use App\Models\Comment;
use App\Enums\SiteSettings;
use App\Mail\NewCommentNotification;
use Filament\Notifications\Notification;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Filament\Schemas\Schema;
use Livewire\Component;

class CommentForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];
    public int $postId;

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @throws \Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn() => !auth()->check())
            ->components([
                MarkdownEditor::make('content')
                    ->label(__('Your comment'))
                    ->toolbarButtons([
                        ['bold', 'italic', 'strike', 'link'],
                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->helperText(__('This field supports Markdown')),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        if (!SiteSettings::COMMENTS_ENABLED->get()) {
            Notification::make()
                ->danger()
                ->title(__('Comments are currently disabled.'))
                ->send();
            return;
        }

        if (!auth()->check()) {
            Notification::make()
                ->danger()
                ->title(__('You must be logged in to comment.'))
                ->send();
            return;
        }

        $comment = Comment::create([
            'content' => $this->form->getState()['content'],
            'post_id' => $this->postId,
            'parent_id' => null,
            'user_id' => auth()->id(),
            'approved_at' => auth()->check() && auth()->user()->is_admin ? now() : null,
        ]);

        $this->form->fill();

        $this->dispatch('comment-created');

        // Send an email notification if the comment needs approval
        if ($comment && $comment->approved_at === null) {
            $contactEmail = SiteSettings::CONTACT_EMAIL->get();
            if ($contactEmail) {
                Mail::to($contactEmail)->send(new NewCommentNotification($comment));
            }
        }

        Notification::make()
            ->success()
            ->title(__('Thanks for your comment!'))
            ->body(__('Your comment will be published once it has been approved.'))
            ->send();
    }

    public function render(): ?View
    {
        if (!SiteSettings::COMMENTS_ENABLED->get()){
            return null;
        }
        return view('livewire.comment-form');
    }
}
