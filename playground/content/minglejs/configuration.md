# Configuration

If you want to change the default configuration, you can publish the configuration file:

```bash
php artisan vendor:publish --tag=mingle-config
```

Which will create a `mingle.php` file in your `config` directory.

```php
<?php

return [

    /**
     * The Blade stack to use, for Mingle to be injected in.
     * Example: `@stack('scripts')`
     *
     */
    'stack' => 'scripts',

    /**
     * The default JavaScript base path.
     *
     */
    'js-basepath' => 'resources/js',

];

```
