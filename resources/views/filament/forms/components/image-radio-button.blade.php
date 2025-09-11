<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
        {{ $getExtraAttributeBag() }}
        class="image-radio-button-group"
    >
        <div @class([
            'grid gap-4',
            'grid-cols-1' => !$isInline(),
            'flex flex-wrap gap-4' => $isInline(),
            'md:grid-cols-2' => !$isInline() && count($getOptions()) > 1,
            'lg:grid-cols-3' => !$isInline() && count($getOptions()) > 2,
        ])>
            @php
                $options = $getOptions();
                $images = $getImages();
            @endphp

            @foreach ($options as $value => $label)
                <label
                    class="relative cursor-pointer group"
                    x-data="{
                        isSelected: false,
                        init() {
                            this.isSelected = this.state === @js($value);
                            this.$watch('state', () => {
                                this.isSelected = this.state === @js($value);
                            });
                        }
                    }"
                >
                    <input
                        type="radio"
                        name="{{ $getId() }}"
                        value="{{ $value }}"
                        x-model="state"
                        class="sr-only"
                    />

                    <div
                        :class="{
                            'ring-2 ring-primary-500 ring-offset-2': isSelected,
                            'ring-1 ring-gray-200 hover:ring-gray-300': !isSelected
                        }"
                        class="relative overflow-hidden rounded-lg bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                    >
                        @if (isset($images[$value]))
                            <div class="aspect-square w-full">
                                <img
                                    src="{{ $images[$value] }}"
                                    alt="{{ $label }}"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                        @endif

                        <div class="p-3">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $label }}
                            </div>
                        </div>

                        <!-- Selected indicator -->
                        <div
                            x-show="isSelected"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-2 right-2"
                        >
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500 text-white">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
</x-dynamic-component>
