@php
    use App\Enums\MainPages;
    use App\Enums\Analytics;
    use App\Enums\SiteSettings;
@endphp
<div {{ $attributes->class('bg-gray-100 dark:bg-gray-800') }}>
    <footer class="container py-8 lg:max-w-(--breakpoint-md) *:[&_a]:hover:underline *:[&_a]:font-medium">
        <div class="flex flex-row justify-center items-center w-full">
            <div>
                <nav class="flex flex-col sm:flex-row gap-y-2 gap-x-8 place-items-center mx-auto w-full">
                    @foreach(SiteSettings::FOOTER_MENU->get() ?? [] as $footerMenuItem)
                        @php
                            $pageKey = data_get($footerMenuItem, 'page');
                            $url = url('/'); // safe default
                            $name = '';

                            if (filled($pageKey) && $pageKey !== 'custom') {
                                if (str_starts_with($pageKey, 'page:')) {
                                    $pageId = (int) str($pageKey)->after('page:')->toString();
                                    $pageModel = \App\Models\Page::find($pageId);

                                    if ($pageModel) {
                                        $url = route('pages.show', ['page' => $pageModel]);
                                        $name = $pageModel->title;
                                    } else {
                                        $url = url('/');
                                        $name = 'Page';
                                    }
                                } else {
                                    $permalink = data_get(SiteSettings::PERMALINKS->get(), $pageKey);
                                    $url = filled($permalink) ? url($permalink) : url('/');
                                    $name = \App\Enums\MainPages::tryFrom($pageKey)?->getTitle() ?? 'Page';
                                }
                            } else {
                                $raw = data_get($footerMenuItem, 'url');

                                if (is_string($raw) && (str_starts_with($raw, 'http') || str_starts_with($raw, '#') || str_starts_with($raw, 'mailto:'))) {
                                    // External, anchor, or mailto: keep as-is
                                    $url = $raw;
                                } else {
                                    $url = filled($raw) ? url($raw) : url('/');
                                }

                                $name = data_get($footerMenuItem, 'name');
                            }

                        @endphp

                        <a
                            data-pan="{{ Analytics::FOOTER_MENU->value }}-{{ str($name)->slug()->toString() }}"
                            href="{{ $url }}"
                            target="{{ data_get($footerMenuItem, 'open_in_new_tab') ? '_blank' : '' }}"
                            @if(!data_get($footerMenuItem, 'open_in_new_tab') && !str_contains($url,'#')) wire:navigate.hover @endif
                            class="text-center"
                        >
                            {{ $name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="mt-8 text-center">
            {!! SiteSettings::FOOTER_TEXT->get() !!}
        </div>

        <p class="mt-8 text-center text-gray-400">{{ str(SiteSettings::COPYRIGHT_TEXT->get())->replace('{year}', date('Y')) }}</p>
    </footer>
</div>
