@php
    use App\Enums\Analytics;
    use App\Support\Nav\DropdownMenu;
@endphp

<x-dropdown>
    <x-slot:btn
        data-pan="{{ Analytics::MAIN_MENU->value }}-more"
        class="transition-colors hover:text-primary-600 dark:hover:text-primary-500 cursor-pointer"
    >
        <div class="menu-icon" x-bind:class="{ 'active': open }">
            <input class="menu-icon__checkbox" type="checkbox" name="more"/>
            <div>
                <span></span> <span></span>
            </div>
        </div>
        {{ __('More') }}
    </x-slot>

    <x-slot:items class="mt-4">
        @foreach(DropdownMenu::getDropdownMenuItems() as $dropdownItem)
            @if($dropdownItem['type'] === 'divider')
                <x-dropdown.divider>
                    {{ $dropdownItem['label'] }}
                </x-dropdown.divider>
            @else
                <x-dropdown.item
                    data-pan="{{ Analytics::DROPDOWN_MENU->value }}-{{ str($dropdownItem['name'])->slug()->toString() }}"
                    href="{{ $dropdownItem['url'] }}"
                    target="{{ $dropdownItem['open_in_new_tab'] ? '_blank' : '' }}"
                >
                    {!! svg($dropdownItem['icon'], 'size-4')->toHtml() !!}
                    {{ $dropdownItem['name'] }}
                </x-dropdown.item>
            @endif
        @endforeach
    </x-slot>
</x-dropdown>
