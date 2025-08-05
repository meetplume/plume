<div>
    <div class="text-gray-500">
        @auth
            {{ __("You're commenting as") }} {{ Auth::user()->name }}.
            <form method="POST" action="{{ \Filament\Facades\Filament::getPanel('user')->getLogoutUrl() }}" class="inline">
                @csrf
                <button type="submit" class="text-primary-600 hover:underline">{{ __("Log out?") }}</button>
            </form>
        @else
            @php
                app('redirect')->setIntendedUrl(url()->current());
            @endphp
            {{ __("You need to ") }}
            <a href="{{ \Filament\Facades\Filament::getPanel('user')->getRegistrationUrl() }}" class="text-primary-600 dark:text-primary-500 hover:underline">{{ __("register") }}</a>
            {{ __(" or ") }}
            <a href="{{ \Filament\Facades\Filament::getPanel('user')->getLoginUrl() }}" class="text-primary-600 dark:text-primary-500 hover:underline">{{ __("log in") }}</a>
            {{ __(" to comment") }}
        @endauth
    </div>
    <form wire:submit="create" class="mt-2">
        {{ $this->form }}

        <x-btn
            primary
            :disabled="$this->form->isDisabled()"
            type="submit"
            class="mt-4"
        >
            {{ __('Comment') }}
        </x-btn>

    </form>

    <x-filament-actions::modals />
</div>
