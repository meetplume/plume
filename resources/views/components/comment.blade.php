@php
    use App\Models\Comment;
    use App\Support\CommentMarkdownOptions;
    use App\Support\CommentMarkdownExtensions;
    /** @var \App\Models\Comment $comment */
@endphp

@props(['comment'])

<div {{ $attributes }} class="@if(!isset($comment->approved_at)) opacity-80 @endif">
    <div class="flex gap-4">

        <div class="w-full">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <img
                        src="{{ $comment->author->getFilamentAvatarUrl() }}"
                        alt="{{ $comment->author->name }}"
                        class="flex-none mt-1 rounded-full shadow-sm shadow-black/5 dark:shadow-white/5 ring-1 ring-black/10 dark:ring-white/10 size-5 md:size-6"
                    />
                    @if($comment->author->isAdmin())
                        <div class="absolute -top-1 -right-1 size-3 md:size-3.5 bg-primary-500 rounded-full flex items-center justify-center text-white text-[8px] md:text-[10px] font-bold border border-white dark:border-gray-800">
                            A
                        </div>
                    @endif
                </div>
                <div class="font-medium @if($comment->author->isAdmin()) text-primary-600 dark:text-primary-500 @endif">
                    {{ $comment->author->name }}
                </div>

                <span class="text-gray-500">
                    {{ $comment->created_at->diffForHumans() }}
                </span>

                @if(!isset($comment->approved_at))
                    <div class="text-xs text-amber-600 dark:text-amber-400">
                        {{ __('Pending approval') }}
                    </div>
                @endif
            </div>

            <div class="beautiful-content px-4 py-3 mt-2 @if(!isset($comment->approved_at)) bg-amber-100 dark:bg-amber-950 @else bg-gray-100 dark:bg-gray-800 @endif rounded-lg @if($comment->author->isAdmin()) ring-1 ring-primary-300 dark:ring-primary-800 @endif">
                {!!
                    wrapPhikiCode(str($comment->content)
                        ->markdown(
                            options: CommentMarkdownOptions::get(),
                            extensions: CommentMarkdownExtensions::get(),
                        )
                        ->sanitizeHtml())
                !!}
            </div>
        </div>
    </div>

    @if ($comment->children->isNotEmpty())
        <div class="grid gap-8 mt-8 ml-11 md:ml-12">
            @foreach ($comment->children as $child)
                <x-comment :comment="$child"/>
            @endforeach
        </div>
    @endif
</div>
