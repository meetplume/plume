<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        @foreach($themes as $themeName => $theme)
            <div
                class="relative border rounded-lg overflow-hidden transition-all duration-200 hover:shadow-lg {{ $activeTheme === $themeName ? 'ring-2 ring-primary-600 border-primary-600' : 'border-gray-300 dark:border-gray-600' }}">
                {{-- Theme Screenshot --}}
                <div class="aspect-[16/9] bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    @if($theme['screenshot'])
                        <img
                            src="{{ $theme['screenshot'] }}"
                            alt="{{ $theme['name'] }} Screenshot"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    {{-- Active Theme Badge --}}
                    @if($activeTheme === $themeName)
                        <div class="absolute top-2 right-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                Active
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Theme Info --}}
                <div class="p-4">
                    <div class="mb-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg">
                            {{ $theme['name'] }}
                        </h3>
                        @if($theme['version'])
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Version {{ $theme['version'] }}
                            </p>
                        @endif
                        @if($theme['description'])
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 line-clamp-2">
                                {{ $theme['description'] }}
                            </p>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        {{ ($this->previewThemeAction)(['theme' => $themeName])->extraAttributes(['class' => 'flex-1']) }}
                        {{ ($this->activateThemeAction)(['theme' => $themeName])->extraAttributes(['class' => 'flex-1']) }}
                    </div>
                </div>
            </div>
        @endforeach
        <div class="relative border rounded-lg overflow-hidden transition-all duration-200 hover:shadow-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 flex flex-col justify-center items-center">
            <div class="text-center">
                <x-phosphor-plus-circle-duotone class="w-12 h-12 text-gray-400 dark:text-gray-500" />
            </div>
            <div class="text-center">{{ __('Add a theme') }}</div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>
