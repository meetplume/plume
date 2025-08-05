<div>
    <div class="text-gray-500">
        @auth
            {{ __("You're commenting as") }} {{ Auth::user()->name }}.
            <form method="POST" action="{{ \Filament\Facades\Filament::getPanel('user')->getLogoutUrl() }}" class="inline">
                @csrf
                <button type="submit" class="text-primary-600 hover:underline">{{ __("Log out?") }}</button>
            </form>
        @else
            {{ __("You need to register or log in to comment ") }}
        @endauth
    </div>
    <form wire:submit="create" class="mt-2">
        {{ $this->form }}

        <button
            :disabled="{{ $this->form->isDisabled() }}"
            type="submit"
            class="font-medium tracking-tight text-white bg-primary-600 rounded-xl transition-colors disabled:bg-gray-100 disabled:hover:bg-gray-100! disabled:text-gray-300! px-[1.3rem] py-[.65rem] hover:bg-primary-500 mt-6"
        >
            {{ __('Comment') }}
        </button>

    </form>

    <x-filament-actions::modals />
</div>
