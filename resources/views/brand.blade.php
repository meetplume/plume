{{--
    Empty on purpose: the header shows the logo and the site name. The host app
    replaces them with any markup by publishing its own
    resources/views/vendor/plume/brand.blade.php, which receives $name, $logo,
    $logoDark, $homeUrl and $vault. Plume wraps it in the link to the vault's
    home, so it must not hold a link of its own.
--}}
