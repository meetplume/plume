@php
    use App\Enums\SiteSettings;
    use App\Enums\Analytics;
    use App\Support\ColoredText;
    use Illuminate\Support\Facades\Storage;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
@endphp
<x-app>

    <div class="container xl:max-w-(--breakpoint-lg) dark">
        <section class="my-12">
            <div class="flex flex-col sm:flex-row justify-between gap-y-8 sm:gap-x-8 sm:items-center lg:gap-x-24 text-center sm:text-left">
                <div class="flex-grow flex-1">
                    <div class="font-heading font-light tracking-tight text-white text-6xl/none md:text-7xl lg:text-8xl text-balance">
                        {!! ColoredText::get(SiteSettings::HERO_TITLE->get()) !!}
                    </div>
                    <div
                        class="mt-5 tracking-tight text-white/75 text-lg/tight sm:text-xl/tight md:text-2xl/tight md:mt-8">
                        {{ SiteSettings::HERO_SUBTITLE->get() }}
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-beige flex  gap-x-10 lg:gap-x-16 mt-10">
                            {{-- TODO: implement socials --}}
                        </div>
                    </div>
                </div>
                <div class="md:max-w-[350px] lg:max-w-[500px] sm:min-w-[200px] lg:min-w-[350px]">
                    @if(SiteSettings::HERO_IMAGE->get() && Storage::disk('public')->exists(SiteSettings::HERO_IMAGE->get()))
                        <img
                            src="{{ Storage::disk('public')->url(SiteSettings::HERO_IMAGE->get()) }}"
                            alt=""
                            class="hero-image w-full {{ SiteSettings::HERO_IMAGE_FULL_WIDTH->get() ? 'object-cover' : 'object-contain' }}"
                            style="height: {{ SiteSettings::HERO_IMAGE_HEIGHT->get() }}px;"
                        >
                    @endif
                </div>
            </div>
        </section>

        @if(filled(SiteSettings::ABOUT_TEXT->get()) && SiteSettings::ABOUT_TEXT->get() !== "<p></p>")
            <section
                class="relative py-12 md:py-16">
                <div class="mb-6 lg:mb-10 flex items-center gap-4">
                    <h2 class="relative w-full bg-black-light text-3xl lg:text-5xl font-semibold text-white">
                        {{ SiteSettings::ABOUT_TITLE->get() }}
                    </h2>
                </div>
                <div class="max-w-4xl mx-auto beautiful-content">
                    {!! RichContentRenderer::make(SiteSettings::ABOUT_TEXT->get() ?? '')->toHtml() !!}
                </div>
            </section>
        @endif

        @if ($posts->isNotEmpty())
            <section class="relative py-12 md:py-16">
                <div class="mb-6 lg:mb-10 flex items-center gap-4">
                    <h2 class="relative w-full bg-black-light text-3xl lg:text-5xl font-semibold text-white">
                        {{ __('Latest posts') }}
                    </h2>
                </div>
                <div class="mb-12">
                    <a
                        class="inline-flex items-center gap-1 text-base font-mono uppercase tracking-widest text-white hover:underline ease-in duration-100"
                        wire:navigate href="{{ route('posts.index') }}"
                        data-pan="{{ Analytics::HOME->value }}-button-browse-all-posts">
                        {{ __('Browse all posts') }}
                        <x-heroicon-o-arrow-up-right class="size-4 ml-1"/>
                    </a>
                </div>
                <div class="relative">
                    @foreach ($posts->take(3) as $post)
                        <x-post :$post/>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app>
