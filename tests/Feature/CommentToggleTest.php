<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Enums\SiteSettings;
use App\Livewire\Comments;
use App\Livewire\CommentForm;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
    $this->comment = Comment::factory()->create([
        'post_id' => $this->post->id,
        'user_id' => $this->user->id,
        'approved_at' => now(),
    ]);
});

test('comments are displayed when enabled', function () {
    SiteSettings::COMMENTS_ENABLED->set(true);

    Livewire::test(Comments::class, ['postId' => $this->post->id])
        ->assertViewHas('commentsCount', 1)
        ->assertSee($this->comment->content);
});

test('comment form is displayed when comments enabled', function () {
    SiteSettings::COMMENTS_ENABLED->set(true);

    Livewire::test(CommentForm::class, ['postId' => $this->post->id])
        ->assertSee('Your comment');
});

test('comment creation works when comments enabled', function () {
    SiteSettings::COMMENTS_ENABLED->set(true);

    $this->actingAs($this->user);

    Livewire::test(CommentForm::class, ['postId' => $this->post->id])
        ->set('data.content', 'Test comment when enabled')
        ->call('create')
        ->assertNotified()
        ->assertDispatched('comment-created');

    // Verify comment was created
    expect(Comment::where('content', 'Test comment when enabled')->exists())->toBeTrue();
});
