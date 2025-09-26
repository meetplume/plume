<x-app title="{{ __('Blog') }}" class="container xl:max-w-(--breakpoint-lg)">
    <x-section :title="__('Latest posts')">

        @if ($posts->isNotEmpty())
            @foreach ($posts as $post)
                <x-post :$post/>
            @endforeach
        @endif

        @if ($posts->hasPages())
            <div class="mt-16">
                {{ $posts->links() }}
            </div>
        @endif

    </x-section>
</x-app>
