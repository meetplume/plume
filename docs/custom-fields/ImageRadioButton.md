# ImageRadioButton Custom Form Field

A custom Filament form field that allows users to select from options displayed as images with radio button functionality.

## Installation

The ImageRadioButton field is already installed in this project at:
- **Class**: `App\Filament\Forms\Components\ImageRadioButton`
- **View**: `resources/views/filament/forms/components/image-radio-button.blade.php`

## Basic Usage

```php
use App\Filament\Forms\Components\ImageRadioButton;

ImageRadioButton::make('theme')
    ->label('Select Theme')
    ->options([
        'light' => 'Light Theme',
        'dark' => 'Dark Theme',
        'auto' => 'Auto Theme',
    ])
    ->images([
        'light' => '/images/themes/light.jpg',
        'dark' => '/images/themes/dark.jpg',
        'auto' => '/images/themes/auto.jpg',
    ])
```

## Configuration Methods

### `options(array | Closure $options)`

Define the available options with their values and labels:

```php
ImageRadioButton::make('layout')
    ->options([
        'grid' => 'Grid View',
        'list' => 'List View',
        'card' => 'Card View',
    ])
```

### `images(array | Closure $images)`

Define the images for each option. The array keys should match the option values:

```php
ImageRadioButton::make('theme')
    ->options(['light' => 'Light', 'dark' => 'Dark'])
    ->images([
        'light' => '/images/light-theme.png',
        'dark' => '/images/dark-theme.png',
    ])
```

### `inline(bool | Closure $inline = true)`

Display options in a horizontal flex layout instead of a grid:

```php
ImageRadioButton::make('size')
    ->options(['small' => 'Small', 'large' => 'Large'])
    ->images(['small' => '/small.jpg', 'large' => '/large.jpg'])
    ->inline() // or ->inline(true)
```

## Features

### Responsive Grid Layout
- **Default**: Single column on mobile, 2 columns on medium screens, 3 columns on large screens
- **Inline**: Horizontal flex layout that wraps on smaller screens

### Visual Feedback
- **Selection Ring**: Primary color ring appears around selected option
- **Hover Effects**: Subtle shadow and ring color changes on hover
- **Check Icon**: Animated checkmark appears on selected option
- **Smooth Transitions**: All state changes are animated

### Accessibility
- **Screen Reader Support**: Hidden radio inputs maintain accessibility
- **Keyboard Navigation**: Supports standard radio button keyboard interactions
- **Proper Labels**: Images have alt text and proper labeling

## Styling

The field uses TailwindCSS v4 classes and supports:
- **Dark Mode**: Automatically adapts to dark mode if implemented
- **Custom Colors**: Uses Filament's primary color scheme
- **Responsive Design**: Mobile-first responsive grid system

## Examples

### Theme Selector
```php
ImageRadioButton::make('theme')
    ->label('Website Theme')
    ->options([
        'light' => 'Light Mode',
        'dark' => 'Dark Mode',
        'system' => 'System Default',
    ])
    ->images([
        'light' => 'https://via.placeholder.com/200x200/f8fafc/1e293b?text=Light',
        'dark' => 'https://via.placeholder.com/200x200/1e293b/f8fafc?text=Dark',
        'system' => 'https://via.placeholder.com/200x200/3b82f6/ffffff?text=Auto',
    ])
    ->default('system')
    ->required()
```

### Layout Selector (Inline)
```php
ImageRadioButton::make('layout')
    ->label('Page Layout')
    ->options([
        'sidebar' => 'With Sidebar',
        'full' => 'Full Width',
        'centered' => 'Centered',
    ])
    ->images([
        'sidebar' => '/layouts/sidebar.svg',
        'full' => '/layouts/full-width.svg',
        'centered' => '/layouts/centered.svg',
    ])
    ->inline()
    ->helperText('Choose how content should be displayed')
```

### Dynamic Options with Closure
```php
ImageRadioButton::make('template')
    ->label('Email Template')
    ->options(function () {
        return Template::where('active', true)
            ->pluck('name', 'id')
            ->toArray();
    })
    ->images(function () {
        return Template::where('active', true)
            ->pluck('preview_image', 'id')
            ->toArray();
    })
```

## Image Requirements

- **Format**: Supports any web-compatible image format (JPG, PNG, SVG, WebP)
- **Size**: Images are displayed in a square aspect ratio (aspect-square)
- **Optimization**: Consider using optimized images for better performance
- **Fallback**: If no image is provided for an option, only the text label is shown

## Integration with Filament Features

### Validation
```php
ImageRadioButton::make('category')
    ->options($categories)
    ->images($categoryImages)
    ->required()
    ->rules(['required', 'in:' . implode(',', array_keys($categories))])
```

### Utility Injection
```php
ImageRadioButton::make('theme')
    ->options(fn ($record) => $record->available_themes)
    ->images(fn ($record) => $record->theme_images)
    ->default(fn ($record) => $record->default_theme)
```

## Complete Example Usage

See `App\Filament\Forms\Examples\ImageRadioButtonExample` for a complete implementation example that can be used in Filament pages or resources.

## Browser Support

- **Modern Browsers**: Full support in Chrome, Firefox, Safari, Edge
- **Alpine.js**: Requires Alpine.js 3.x (included with Livewire 3)
- **CSS Grid**: Uses CSS Grid for responsive layout
