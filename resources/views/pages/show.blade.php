@php
    use App\Enums\Analytics;
@endphp
<x-app
    :description="$page->description"
    :title="$page->title"
    data-pan="{{ Analytics::POSTS->value }}-page-{{ $page->slug }}"
>
    <div class="container">
        <div class="max-w-4xl mx-auto">
            <article>
                <h1 class="mt-6 font-heading font-medium tracking-tight text-center text-black dark:text-white text-balance md:mt-8 text-3xl/none sm:text-4xl/none lg:text-5xl/none">
                    {{ $page->title }}
                </h1>

                <div class="beautiful-content mt-8">
                    {!! $page->formattedContent !!}
                </div>
            </article>
        </div>
    </div>
</x-app>
