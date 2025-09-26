# Themes

This section covers the theme system, which allows you to create, manage, and customize WordPress-style themes in your Laravel application. It includes a fluent API, Blade directives, custom fields, and theme upload functionality.

## Overview

The theme system allows you to:

- Create custom themes with their own layouts, styles, and functionality
- Define custom fields that users can configure for each theme
- Upload and install themes via ZIP files
- Use a [fluent API](theme-api.md) for theme interactions
- Override views and templates on a per-theme basis
- Integrate seamlessly with Vite for asset processing

## Theme Directory Structure

Themes are stored in `resources/themes/` and follow this structure:

```
resources/themes/
├── default/
│   ├── theme.json              # Theme metadata and configuration
│   ├── style.css               # Main theme stylesheet (processed by Vite)
│   ├── screenshot.png          # Theme preview image
│   ├── views/                  # View overrides
│   │   ├── components/
│   │   │   └── app.blade.php   # Layout override
│   │   ├── posts/
│   │   └── pages/
│   └── partials/               # Theme-specific partials
│       ├── hero.blade.php
│       └── sidebar.blade.php
├── magazine/
└── minimal/
```

## Theme Configuration (theme.json)

Each theme must include a `theme.json` file with metadata and configuration:

```json
{
  "name": "Magazine Theme",
  "description": "A modern magazine-style layout with featured posts",
  "version": "1.0.0",
  "author": "Your Name",
  "screenshot": "screenshot.png",
  "settings": {
    "primary_color": "#dc2626",
    "neutral_color": "slate",
    "heading_font": "Inter",
    "body_font": "Inter"
  },
  "fields": [
    {
      "key": "accent_color",
      "type": "colorpicker",
      "label": "Accent Color",
      "helperText": "Secondary color used for highlights",
      "cssVariable": "--magazine-accent-color",
      "default": "#f59e0b"
    },
    {
      "key": "layout_style",
      "type": "select",
      "label": "Layout Style",
      "options": {
        "standard": "Standard Grid",
        "masonry": "Masonry Layout",
        "featured": "Featured Posts First"
      },
      "default": "standard"
    }
  ]
}
```

## Custom Field Types

The theme system supports various field types that can be defined in `theme.json`:

### Text Input
```json
{
  "key": "header_tagline",
  "type": "text",
  "label": "Header Tagline",
  "placeholder": "Your tagline...",
  "helperText": "Displayed below the site title",
  "maxLength": 100,
  "default": "Breaking news and stories"
}
```

### Color Picker
```json
{
  "key": "accent_color",
  "type": "colorpicker",
  "label": "Accent Color",
  "helperText": "Secondary color used for highlights",
  "cssVariable": "--magazine-accent-color",
  "default": "#f59e0b"
}
```

### Palette Color Picker
```json
{
  "key": "category_colors",
  "type": "palettecolorpicker",
  "label": "Category Badge Color",
  "colors": {
    "red": "Red",
    "blue": "Blue",
    "green": "Green"
  },
  "default": "red"
}
```

### Select Dropdown
```json
{
  "key": "layout_style",
  "type": "select",
  "label": "Layout Style",
  "options": {
    "standard": "Standard Grid",
    "masonry": "Masonry Layout"
  },
  "searchable": true,
  "default": "standard"
}
```

### Font Picker
```json
{
  "key": "content_font",
  "type": "fontpicker",
  "label": "Content Font",
  "helperText": "Font used for article content",
  "default": "Inter"
}
```

## How to Use Custom Fields in Your Theme

In a blade template, you can access custom fields using the [fluent API](theme-api.md) methods:

Example:
```blade
{{-- resources/themes/magazine/views/components/app.blade.php --}}
@php
    $accentColor = theme()->fields('theme_magazine_accent_color')->value();
    $layoutStyle = theme()->fields('theme_magazine_layout')->value();
@endphp
```

## Blade Directives

The theme system provides several Blade directives for theme integration:

### @themeAsset
Generate URLs for theme-specific assets:

```blade
{{-- Theme CSS and JS --}}
<link rel="stylesheet" href="@themeAsset('css/components.css')">
<script src="@themeAsset('js/theme.js')"></script>

{{-- Theme images --}}
<img src="@themeAsset('images/logo.png')" alt="Theme Logo">
```

### @themePartial
Include theme-specific partials:

```blade
{{-- Include theme's hero partial --}}
@themePartial('hero')

{{-- Include theme's sidebar partial --}}
@themePartial('sidebar')
```

### @themeConfig
Get theme configuration values:

```blade
{{-- Get theme name --}}
<h1>@themeConfig('name')</h1>

{{-- Get nested config values --}}
<div style="max-width: @themeConfig('settings.layout.container_max_width')">
    Content here
</div>
```

### @themeSettings
Or simpler, use the `@themeSettings` directive to access the theme's settings directly:

```blade
{{-- Get a setting value --}}
<div style="color: @themeSettings('primary_color')">
    Content here
</div>
```

### @hasThemePartial
Conditionally include partials:

```blade
@hasThemePartial('hero')
    @themePartial('hero')
@endHasThemePartial

@hasThemePartial('sidebar')
    <aside>
        @themePartial('sidebar')
    </aside>
@else
    <aside>
        <p>No sidebar available</p>
    </aside>
@endHasThemePartial
```

## Artisan Commands

### Create a New Theme
```bash
# Create a new theme from scratch
php artisan make:theme my-theme

# Create a theme by copying from existing theme
php artisan make:theme my-theme --copy-from=default
```

### Activate a Theme
```bash
php artisan theme:activate magazine
```

### List Available Themes
```bash
php artisan theme:list
```

### Publish Theme Assets
```bash
# Publish assets for specific theme
php artisan theme:publish magazine

# Publish assets for all themes
php artisan theme:publish

# Force republish existing assets
php artisan theme:publish --force
```

## Theme Upload Functionality

Users can upload custom themes through the admin interface:

1. **ZIP File Structure**: Themes should be packaged as ZIP files with the theme.json at the root or one level deep
2. **Validation**: The system validates that the ZIP contains a valid theme.json file
3. **Installation**: Themes are extracted to `storage/app/private/themes/`
4. **Auto-detection**: Uploaded themes automatically appear in the theme selector

### Programmatic Upload
```php
$themeService = app(ThemeService::class);
$result = $themeService->uploadAndInstallTheme($filePath);

if ($result['success']) {
    echo "Theme '{$result['theme_name']}' installed successfully!";
} else {
    echo "Upload failed: " . $result['message'];
}
```

## View Overrides

Themes can override any view in the application by placing files in the `views/` directory:

```
resources/themes/magazine/
└── views/
    ├── components/
    │   └── app.blade.php     # Override main layout
    ├── posts/
    │   ├── index.blade.php   # Override post listing
    │   └── show.blade.php    # Override single post
    └── pages/
        └── home.blade.php    # Override home page
```

The theme system uses Laravel's view finder to automatically check for theme-specific views first, then fall back to default views.

## Best Practices

### Theme Development
1. **Always include a theme.json** with proper metadata
2. **Provide a screenshot.png** for theme preview (recommended size: 1200x900px)
3. **Use semantic CSS variables** for customizable properties
4. **Test responsive design** on all screen sizes
5. **Follow naming conventions** for consistency

### CSS Organization
```css
/* Use CSS custom properties for theme customization */
:root {
    --theme-primary: #dc2626;
    --theme-container-max-width: 1400px;
    --theme-border-radius: 0.75rem;
}

/* Scope styles to theme wrapper */
.theme-wrapper.magazine {
    /* Theme-specific styles */
}
```

## Examples

### Complete Theme Implementation

```php
// Create and activate a new theme
php artisan make:theme portfolio --copy-from=default
php artisan theme:activate portfolio

// In a controller or component
$theme = theme();
$portfolioColor = $theme->fields('theme_portfolio_accent_color')->value();
$layoutStyle = $theme->fields('theme_portfolio_layout')->value();
```

### Theme-aware Component
```blade
{{-- resources/themes/portfolio/views/components/project-grid.blade.php --}}
@php
    $accentColor = theme()->fields('theme_portfolio_accent_color')->value();
    $columns = theme()->fields('theme_portfolio_grid_columns')->value() ?? 3;
@endphp

<div class="project-grid grid-cols-{{ $columns }}" style="--accent: {{ $accentColor }}">
    @foreach($projects as $project)
        <div class="project-card">
            <h3>{{ $project->title }}</h3>
            <p>{{ $project->description }}</p>
        </div>
    @endforeach
</div>
```

### Dynamic Theme Switching
```php
// Programmatically switch themes and apply settings
$themeService = app(ThemeService::class);

if ($themeService->activateTheme('magazine')) {
    // Theme switched successfully
    // Custom field defaults are automatically applied
    
    // Get theme-specific configuration
    $config = $themeService->getThemeConfig();
    $features = $config['supports'] ?? [];
    
    if ($features['sidebar'] ?? false) {
        // Theme supports sidebar
    }
}
```
