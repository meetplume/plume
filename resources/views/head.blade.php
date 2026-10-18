{{--
    Empty on purpose: the host app adds to the <head> of every Plume page by
    publishing its own resources/views/vendor/plume/head.blade.php (styles
    mapped to the app's theme, favicon, analytics...). It comes after Plume's
    stylesheet, so its CSS variables win.
--}}
