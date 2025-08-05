<section
    id="comments"
    class="mt-24 scroll-mt-4"
>
    <h1 class="font-bold tracking-widest text-center text-black dark:text-white uppercase">
        {{ trans_choice(':count comment|:count comments', $commentsCount) }}
    </h1>

    @if ($comments->isNotEmpty())
        <div class="flex flex-col gap-8 mt-8">
            @foreach ($comments as $comment)
                @if(isset($comment->approved_at) || (Auth::check() && (Auth::user()->isAdmin() || $comment->author->is(Auth::user()))))
                    <x-comment :$comment />
                @endif
            @endforeach
        </div>
    @endif
</section>
