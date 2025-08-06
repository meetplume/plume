<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\On;

class Comments extends Component
{
    public int $postId;

    #[On('comment-created')]
    public function refreshComments()
    {
        // This method will be called when the comment-created event is dispatched
        // The component will automatically refresh
    }

    public function render() : View
    {
        return view('livewire.comments', [
            'comments' => Comment::query()
                ->where('post_id', $this->postId)
                ->whereNull('parent_id')
                ->paginate(30),
            'commentsCount' => Comment::query()
                ->whereNotNull('approved_at')
                ->where('post_id', $this->postId)
                ->count(),
        ]);
    }
}
