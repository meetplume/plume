# Theme API

The fluent Theme API provides a comprehensive system for creating, managing, and customizing WordPress-style themes in your Laravel application.

## Basic Usage

```php
// Get the theme service instance
$theme = theme();

// Get active theme name
$activeTheme = theme()->active(); // 'magazine'

// Get theme configuration
$config = theme()->config();

// Check if a theme exists
$exists = theme()->exists('magazine'); // true

// Activate a theme
$success = theme()->activate('magazine');
```

## Working with Specific Themes

```php
// Work with a specific theme (not necessarily active)
$magazineTheme = theme('magazine');
$minimalTheme = theme('minimal');

// Get configuration for a specific theme
$magazineConfig = theme('magazine')->config();
```

## Theme Fields API

The fields API provides a fluent interface for working with theme custom fields:

```php
// Get default value for a field
$defaultColor = theme()->fields('theme_magazine_accent_color')->default();

// Get current value
$currentColor = theme()->fields('theme_magazine_accent_color')->value();

// Set a value
theme()->fields('theme_magazine_accent_color')->set('#ff0000');

// Get the setting key
$key = theme()->fields('theme_magazine_accent_color')->key();

// Get field definition from theme.json
$definition = theme()->fields('theme_magazine_accent_color')->definition();

// Check if field exists
$exists = theme()->fields('theme_magazine_accent_color')->exists();
```

## Working with All Fields

```php
// Get all fields for the active theme
$allFields = theme()->fields()->all();

// Get all default values for the active theme
$defaults = theme()->fields()->default();
// Returns: ['accent_color' => '#f59e0b', 'layout_style' => 'standard']

// Work with specific theme's fields
$magazineDefaults = theme('magazine')->fields()->default();
```
