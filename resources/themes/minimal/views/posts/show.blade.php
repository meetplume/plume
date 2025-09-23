@php
    use App\Support\TableOfContents;
    use App\Enums\Analytics;
@endphp
<x-app
    :description="$post->description"
    :image="$post->image"
    :title="$post->title"
    data-pan="{{ Analytics::POSTS->value }}-{{ $post->slug }}"
>
    <div class="container">
        <div class="max-w-3xl mx-auto">
            <article>
                @if(auth()->check() && auth()->user()->isAdmin() && ! $post->isPublished())
                    <x-alert
                        type="warning"
                        class="mb-4"
                        :heading="match(true) {
                            $post->isDraft() => __('This post is a draft'),
                            $post->isPlanned() => __('This post is planned') . ' ' . $post->published_at->diffForHumans()
                        }">
                        {{ __('The current post is not published yet. Only logged in administrators can see it.') }}
                    </x-alert>
                @endif

                <h1 class="font-heading font-medium tracking-tight text-center text-black dark:text-white text-balance mt-12 md:mt-16 text-4xl/none sm:text-5xl/none lg:text-6xl/none">
                    {{ $post->title }}
                </h1>

                @if ($post->categories->isNotEmpty() || $post->tags->isNotEmpty())
                    <div class="flex flex-col gap-2 mt-6">
                        @if ($post->categories->isNotEmpty())
                            <div class="flex justify-center flex-wrap place-self-center font-mono uppercase tracking-widest">
                                @foreach ($post->categories->take(3) as $category)
                                    <a wire:navigate
                                       href="{{ route('categories.show', $category) }}"
                                       class="hover:underline">
                                        {{ $category->name }}
                                    </a>
                                    @if (! $loop->last)
                                        &comma;&nbsp;
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if ($post->tags->isNotEmpty())
                            <div class="flex gap-4 justify-center flex-wrap place-self-center">
                                @foreach ($post->tags->take(10) as $tag)
                                    <a
                                        wire:navigate href="{{ route('tags.show', ['tag' => $tag]) }}"
                                        class="text-sm font-mono hover:underline">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-12 md:mt-16 relative aspect-[16/7] rounded-xl ring-1 shadow-xl ring-black/5">
                    @if ($post->placeholder)
                        <img
                            src="{{ $post->placeholder }}"
                            alt=""
                            aria-hidden="true"
                            class="object-cover w-full rounded-xl aspect-[16/7] absolute inset-0 transition-opacity duration-700 ease-in-out"
                        />
                    @endif

                    @if ($post->image_url)
                        <img
                            src="{{ $post->image_url }}"
                            alt="{{ $post->title }}"
                            onload="this.style.opacity='1'"
                            class="object-cover w-full rounded-xl aspect-[16/7] transition-opacity duration-[0.8s] ease-in-out relative opacity-0"
                        />
                    @endif
                </div>


                <div class="mt-6 md:mt-8">
                    <div class="flex gap-2 justify-center flex-wrap place-self-center text-gray-400">
                        <div>
                            {{ ($post->updated_at ?? $post->published_at ?? $post->created_at)->isoFormat('ll') }}
                        </div>
                        &middot;
                        <a href="#comments" class="">
                            {{ $post->approved_comments_count }} {{ trans_choice('comment|comments', $post->approved_comments_count) }}
                        </a>
                        &middot;
                        <div>
                            {{ $post->read_time }} {{ trans_choice('minute read|minutes read', $post->read_time) }}
                        </div>
                    </div>

                    @php
                        $tableOfContents = TableOfContents::generate($post->body);
                    @endphp

                    @if(!blank($tableOfContents))
                        <div class="mt-12">
                            {!! $tableOfContents->render() !!}
                        </div>
                        <hr class="mt-12 text-gray-100 dark:text-gray-800"/>
                    @endif

                    <div class="beautiful-content mt-8">
                        {!! $post->formattedContent !!}
                    </div>

                    @if (! empty($relatedPosts))
                        <hr class="mt-12 text-gray-100 dark:text-gray-800"/>

                        <h3 class="text-3xl lg:text-5xl font-medium text-gray-900 dark:text-gray-200 mt-8 mb-2">
                            {{ __('Continue reading') }}
                        </h3>
                        <p class="tracking-tight text-gray-400 text-lg/tight mb-8">
                            {{ __('Here are some other articles you may like') }}
                        </p>

                        <ul class="grid gap-10 gap-y-16 xl:gap-x-16 sm:grid-cols-2">
                            @foreach ($relatedPosts as $relatedPost)
                                <li>
                                    <x-post :post="$relatedPost" variant="mini"/>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </article>

            @if ($post->comments_count)
                <div class="mt-24 max-w-2xl m-auto">
                    <livewire:comments :post-id="$post->id" />
                </div>
            @endif

            <div class="mt-16 max-w-2xl m-auto">
                <livewire:comment-form :post-id="$post->id" />
            </div>

        </div>

    </div>

    @if ($post->published_at)
        @php
            $structuredData = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'author' => [
                    '@type' => 'Person',
                    'name' => $post->author->name,
                    'url' => route('home') . '#about',
                ],
                'headline' => $post->title,
                'description' => $post->description,
                'image' => $post->image_url,
                'datePublished' => $post->published_at->toIso8601String(),
                'dateModified' => ($post->updated_at ?? $post->published_at)->toIso8601String(),
            ];
        @endphp

        <script type="application/ld+json">
            {!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
</x-app>
