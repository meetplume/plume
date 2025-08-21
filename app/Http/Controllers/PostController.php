<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() : View
    {
        return view('posts.index', [
            'posts' => Post::query()->published()->latest('published_at')->paginate(24),
        ]);
    }

    public function show(Request $request, Post $post) : View
    {
        abort_unless($request->user()?->isAdmin() || $post->isPublished(), 404);

        $relatedPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->where('id', '<>', $post->id)
            ->limit(10)
            ->get()
            ->shuffle()
            ->take(2);

        return view('posts.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
