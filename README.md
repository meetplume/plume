# 🪶 Plume

> THIS IS A WORK IN PROGRESS!

![minimal-theme](https://github.com/user-attachments/assets/f0796951-9ad5-4322-b0aa-b625bc60ec56)

Plume is a simple, extensible publishing for devs, powered by Filament 4 & TALL Stack

Install, log in as admin, tweak some settings and push to prod ☁️.

It's free and open source. 

## Stack

- Laravel v12
- Livewire
- AlpineJS
- TailwindCSS v4
- Filament v4

## Features

### Full Admin Panel (built with Filament) with:
  - [x] Blog posts
  - [x] Categories and Tags
  - [x] Menu Builder
  - [x] Analytics with panphp/pan
  - [x] Multilingual/Translatable
  - [x] Comments
  - [x] Mail notifications
  - [x] CMS Pages
  - [x] Themes
  - [x] Configurable Settings like fonts, languages, social media links, etc.
  - and more.

### Front end (built with TALL Stack), with:
  - [x] Configurable Homepage
  - [x] Blog posts with code blocks, table of contents and more
  - [x] Dark mode switcher
  - [x] Social links
  - [x] Beautiful Handcrafted Themes
  - and more.

### Misc

Fonts are loaded through [fonts.bunny.net](https://fonts.bunny.net/) so your blog is GDPR friendly.

## Installation

### 1. Clone the repo

```bash
git clone https://github.com/meetplume/plume.git
```

### 2. Install dependencies

```bash
composer install
```

### 3. Run the installer

You will be prompted for several configuration options.

```bash
php artisan blog:install
```

### 4. Enjoy your new blog!

## Docs

### Themes Docs
- [Custom Themes](/docs/themes/themes.md)
- [Theme API](/docs/themes/theme-api.md)

### Custom fields
- [Image Radio Button](docs/custom-fields/ImageRadioButton.md)

## Testing

You can run the test suite with the following command:

```bash
php artisan test --parallel
```

## Contribute

Contributions are welcome!

## Credits

Some parts of this blog are inspired by https://github.com/benjamincrozat/blog-v5.

Also, big thanks to @awcodes for his help on TipTap plugins and table of contents!
