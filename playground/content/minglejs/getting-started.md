# Install Mingle


There are plenty of combinations where Mingle can be installed. In this page, let's cover with Laravel Breeze.

You might have an application already, so you might want to skip this step.


In this page, let's start from a fresh new installation of Laravel Breeze, like so:

```bash
laravel new --stack=livewire --breeze demo
cd demo
````

Next (or first), let's install the PHP package:

```bash
composer require ijpatricio/mingle
```

You can have both flavors of MingleJS, React and Vue, installed at the same time. Let's cover them individually.

For Vue:

```bash
npm i vue @vitejs/plugin-vue
```

For React:

```bash
npm i @vitejs/plugin-react react react-dom
```


::: warning
It's a good idea to commit your Git changes before proceeding. If anything goes wrong, you can always revert the changes.
:::

We're now ready to make the significant changes to the project files. If you want to make them manually, check [Manual Instructions](manual-instructions.md). For an automated attempt, run:

```bash
php artisan mingle:install --with-demo
```

Or if it's not your first time, you can skip the demo:

```bash
php artisan mingle:install
```

Let's see how to create Mingles in the [Creating Mingles](creating-mingles.md) page.

