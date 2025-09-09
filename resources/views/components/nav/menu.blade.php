@php
    use App\Support\Nav\NavMenu;
@endphp

@foreach(NavMenu::getMainMenuItems() as $menuItem)
    <x-nav.menu-item :menuItem="$menuItem"/>
@endforeach
