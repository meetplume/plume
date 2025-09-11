<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        @foreach($themes as $themeName => $theme)
            <div
                class="group relative border rounded-lg overflow-hidden transition-all duration-200 hover:shadow-lg {{ $activeTheme === $themeName ? 'ring-2 ring-success-600 border-success-600 shadow-success-600/30 shadow-xl' : 'border-gray-300 dark:border-gray-600' }}">
                {{-- Theme Screenshot with Overlay Content --}}
                <div class="relative aspect-[4/3] bg-gray-100 dark:bg-gray-800 overflow-hidden">
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

                    {{-- Overlay with gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    {{-- Active Theme Badge --}}
                    @if($activeTheme === $themeName)
                        <div class="absolute top-2 right-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-800 dark:text-success-200">
                                <x-phosphor-check-bold class="w-4 h-4 mr-1" />
                                Active
                            </span>
                        </div>
                    @endif

                    {{-- Theme Info Overlay --}}
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white group-hover:pb-6 transition-all duration-300">
                        <div class="mb-3">
                            <h3 class="font-semibold text-white text-lg">
                                {{ str($theme['name'])->limit(28) }}
                            </h3>
                            @if($theme['version'])
                                <div class="overflow-hidden max-h-0 group-hover:max-h-10 transition-all duration-300 group-hover:mt-1">
                                    <x-filament::badge
                                        color="primary"
                                        class="opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300"
                                        size="sm"
                                    >
                                        v{{ $theme['version'] }}
                                    </x-filament::badge>
                                </div>
                            @endif
                            @if($theme['description'])
                                <div class="overflow-hidden max-h-0 group-hover:max-h-20 transition-all duration-300 group-hover:mt-2">
                                    <p class="text-sm text-gray-100 line-clamp-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 delay-75">
                                        {{ str($theme['description'])->limit(70) }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            {{ ($this->previewThemeAction)(['theme' => $themeName])->extraAttributes(['class' => 'flex-1']) }}
                            {{ ($this->activateThemeAction)(['theme' => $themeName])->extraAttributes(['class' => 'flex-1']) }}
                        </div>
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
