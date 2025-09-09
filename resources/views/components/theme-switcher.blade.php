@php
    use App\Enums\SiteSettings;
@endphp

@if(SiteSettings::DARK_MODE->get() === 'switcher')
    <button
        type="button"
        x-data="{
            theme: localStorage.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
            init() {
                document.documentElement.classList.toggle('dark', this.theme === 'dark');

                // Listen for system theme changes only if user hasn't explicitly set a theme
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addEventListener('change', (e) => {
                    // Only auto-update if no explicit theme is stored
                    if (!localStorage.theme) {
                        this.theme = e.matches ? 'dark' : 'light';
                        document.documentElement.classList.toggle('dark', this.theme === 'dark');
                    }
                });
            },
            toggle() {
                this.theme = this.theme === 'light' ? 'dark' : 'light';
                localStorage.theme = this.theme;
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
            }
        }"
            x-on:click="toggle()"
            class="transition-colors hover:text-primary-600 dark:hover:text-primary-500 cursor-pointer"
            :aria-label="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
        >
        <span x-show="theme !== 'dark'">
            @svg('heroicon-o-moon', 'size-6')
        </span> <span x-show="theme === 'dark'">
            @svg('heroicon-o-sun', 'size-6')
        </span>
    </button>
@endif
