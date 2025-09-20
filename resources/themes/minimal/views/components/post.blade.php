@props(['post'])

<a {{ $attributes->class('grid grid-cols-12 mb-16 last:mb-0 md:mb-32 items-center') }} wire:navigate href="{{ route('posts.show', ['post' => $post]) }}">
    <div class="col-span-full md:col-span-6 md:col-start-7 md:row-start-1 h-full md:flex md:justify-center md:items-center">
        <div class="relative w-full h-full mb-6 sm:mb-0 min-h-[125px] md:min-h-[250px] md:max-h-[250px] lg:min-h-[300px] lg:max-h-[300px]">
            <img src="{{ $post->image_url }}" alt="{{ $post->title  }}" class="object-cover rounded-xl ring-1 shadow-md transition-opacity shadow-black/5 dark:shadow-white/5 aspect-video hover:opacity-50 ring-black/5 dark:ring-white/5" />
        </div>
    </div>
    <div class="col-span-full md:col-span-5 md:row-start-1 py-4 lg:py-8">
        <div class="flex justify-start items-center gap-3 text-sm lg:text-base font-light mb-3">
            @if (! empty($post->categories))
                {{ $categoriesList = implode(', ', $post->categories->take(3)->pluck('name')->toArray()) }}
            @endif
            <span>{{ ($post->updated_at ?? $post->published_at)->isoFormat('l') }}</span>
        </div>
        <h3 class="font-bold text-lg text-white mb-4 md:text-2xl lg:text-3xl">
            {{ $post->title }}
        </h3>
        <div class="">
            {!! $post->description ?? '' !!}
        </div>
    </div>
</a>
