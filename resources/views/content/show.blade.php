@php
    use App\Enums\Analytics;use Illuminate\Support\Carbon;

    $title = $object?->matter()['title'] ?? '';
    $description = $object?->matter()['description'] ?? '';
    $category = $object?->matter()['category'] ?? '';
    $published = $object?->matter()['published'] ?? true;
    $date = $object?->matter()['date'] ?? '';
    $author = $object?->matter()['author'] ?? '';
    $tags = $object?->matter()['tags'] ?? [];
@endphp

<x-app
    :description="$description"
    :title="$title"
    data-pan="{{ Analytics::CONTENT->value }}-{{ str($title)->slug() }}"
>
    <div class="container">
        <div class="max-w-4xl mx-auto">
            <article>
                @if(auth()->check() && auth()->user()->isAdmin() && ! $published)
                    <x-alert
                        type="warning"
                        class="mb-4"
                        :heading="__('This content is a draft')">
                        {{ __('The current content is not published yet. Only logged in administrators can see it.') }}
                    </x-alert>
                @endif

                <h1 class="mt-12 font-heading font-medium tracking-tight text-center text-black dark:text-white text-balance md:mt-16 text-3xl/none sm:text-4xl/none lg:text-5xl/none">
                    {{ $title }}
                </h1>

                <div class="mt-12 md:mt-16">

                    <div class="grid grid-cols-2 gap-4 text-sm leading-tight md:grid-cols-4">
                        <div class="rounded-xl border border-black/8 dark:border-white/10 p-[5px]">
                            <div
                                class="flex-1 p-3 text-center text-sm rounded-lg border border-black/8 dark:border-white/10">
                                <x-heroicon-o-calendar class="mx-auto mb-2 opacity-75 size-6"/>

                                @if($published)
                                    {{ __('Modified') }}
                                @else
                                    {{ __('Drafted') }}
                                @endif

                                <br/>

                                @if(!empty($date))
                                    {{ Carbon::parse($date)->isoFormat('ll') }}
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl border border-black/8 dark:border-white/10 p-[5px]">
                            <div
                                class="flex-1 p-3 text-center text-sm rounded-lg border border-black/8 dark:border-white/10">
                                <x-heroicon-o-user class="mx-auto mb-2 opacity-75 size-6"/>
                                {{ __('Written by') }}<br/>
                                {{ $author }}
                            </div>
                        </div>

                    </div>

                    @if (! empty($category))
                        <div class="flex gap-2 mt-6 justify-center flex-wrap place-self-center">
                            <a wire:navigate href="#"
                               class="px-2 py-1 text-xs font-medium uppercase rounded-sm border border-gray-200 dark:border-gray-700 transition-colors hover:border-primary-300 dark:hover:border-primary-700 hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $category }}
                            </a>
                        </div>
                    @endif

                    @if (! empty($tags))
                        <div class="flex gap-2 mt-6 justify-center flex-wrap place-self-center">
                            @foreach ($tags as $tag)
                                <a wire:navigate href="#"
                                   class="px-2 py-1 text-sm rounded-sm bg-gray-50 dark:bg-gray-800 transition-colors hover:bg-primary-100 hover:text-primary-600 dark:hover:bg-primary-950 dark:hover:text-primary-300">
                                    #{{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="beautiful-content mt-8">
                        @if(isset($dangerouslyAllowBladeRender) && $dangerouslyAllowBladeRender === true)
                            {!! str($object->body())->markdown() !!}
                        @else
                            {{ str($object->body())->markdown() }}
                        @endif
                    </div>
                </div>
            </article>
        </div>
    </div>
</x-app>
