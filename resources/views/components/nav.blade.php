<nav {{ $attributes->class('flex items-center gap-6 md:gap-8 justify-between header-nav') }}>

    <x-logo/>

    <div class="menu flex items-center gap-6 md:gap-8 font-normal text-sm">

        <x-theme-switcher />

        <x-language-switcher/>

        <x-nav.menu />

        <x-nav.more-dropdown />

    </div>

</nav>
