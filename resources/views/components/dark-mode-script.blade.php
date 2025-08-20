<script>
    @php $darkMode = App\Enums\SiteSettings::DARK_MODE->get(); @endphp

    @if($darkMode === 'always_dark')
        document.documentElement.classList.add( "dark" );
    @elseif($darkMode === 'always_light')
        document.documentElement.classList.remove( "dark" );
    @elseif($darkMode === 'always_system')
        document.documentElement.classList.toggle(
            "dark",
            window.matchMedia( "(prefers-color-scheme: dark)" ).matches
        );
        const mediaQuery = window.matchMedia( '(prefers-color-scheme: dark)' );
        mediaQuery.addEventListener( 'change', (e) => {
            document.documentElement.classList.toggle( 'dark', e.matches );
        } );
    @else
        // Default 'switcher' behavior
        document.documentElement.classList.toggle(
            "dark",
            localStorage.theme === "dark" ||
            (!("theme" in localStorage) && window.matchMedia( "(prefers-color-scheme: dark)" ).matches),
        );
    @endif
</script>
