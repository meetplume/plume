<?php

namespace App\Services;

use App\Enums\SiteSettings;
use Illuminate\Support\Collection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use CharlieEtienne\FilamentFontPicker\FontPicker;
use Awcodes\Palette\Forms\Components\ColorPicker as PaletteColorPicker;
use Filament\Support\Colors\Color;

class ThemeFieldsService
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Get field definitions for the active theme
     */
    public function getActiveThemeFields(): array
    {
        $config = $this->themeService->getThemeConfig();
        return $config['fields'] ?? [];
    }

    /**
     * Get field definitions for a specific theme
     */
    public function getThemeFields(string $theme): array
    {
        $configPath = resource_path("themes/{$theme}/theme.json");

        if (!file_exists($configPath)) {
            return [];
        }

        $config = json_decode(file_get_contents($configPath), true);
        return $config['fields'] ?? [];
    }

    /**
     * Convert theme field definition to Filament form component
     */
    public function createFormComponent(array $fieldDefinition): mixed
    {
        $type = $fieldDefinition['type'] ?? 'text';
        $key = $fieldDefinition['key'] ?? '';
        $label = $fieldDefinition['label'] ?? ucfirst(str_replace('_', ' ', $key));

        $component = match ($type) {
            'text' => $this->createTextInput($key, $label, $fieldDefinition),
            'colorpicker' => $this->createColorPicker($key, $label, $fieldDefinition),
            'palettecolorpicker' => $this->createPaletteColorPicker($key, $label, $fieldDefinition),
            'select' => $this->createSelect($key, $label, $fieldDefinition),
            'fontpicker' => $this->createFontPicker($key, $label, $fieldDefinition),
            default => $this->createTextInput($key, $label, $fieldDefinition),
        };

        // Apply common properties
        if (isset($fieldDefinition['helperText'])) {
            $component = $component->helperText($fieldDefinition['helperText']);
        }

        if (isset($fieldDefinition['required']) && $fieldDefinition['required']) {
            $component = $component->required();
        }

        return $component;
    }

    /**
     * Create text input component
     */
    protected function createTextInput(string $key, string $label, array $definition): TextInput
    {
        $component = TextInput::make($this->getThemeFieldKey($key))
            ->label($label);

        if (isset($definition['placeholder'])) {
            $component = $component->placeholder($definition['placeholder']);
        }

        if (isset($definition['maxLength'])) {
            $component = $component->maxLength($definition['maxLength']);
        }

        return $component;
    }

    /**
     * Create color picker component
     */
    protected function createColorPicker(string $key, string $label, array $definition): ColorPicker
    {
        return ColorPicker::make($this->getThemeFieldKey($key))
            ->label($label);
    }

    /**
     * Create palette color picker component
     */
    protected function createPaletteColorPicker(string $key, string $label, array $definition): PaletteColorPicker
    {
        $component = PaletteColorPicker::make($this->getThemeFieldKey($key))
            ->label($label)
            ->storeAsKey();

        if (isset($definition['colors'])) {
            $colors = [];
            foreach ($definition['colors'] as $colorKey => $colorValue) {
                if (is_string($colorValue) && class_exists($colorValue)) {
                    $colors[$colorKey] = $colorValue;
                } else {
                    // Try to map to Color class
                    $colorName = ucfirst($colorKey);
                    if (defined(Color::class . "::$colorName")) {
                        $colors[$colorKey] = constant(Color::class . "::$colorName");
                    }
                }
            }
            if (!empty($colors)) {
                $component = $component->colors($colors);
            }
        }

        return $component;
    }

    /**
     * Create select component
     */
    protected function createSelect(string $key, string $label, array $definition): Select
    {
        $component = Select::make($this->getThemeFieldKey($key))
            ->label($label);

        if (isset($definition['options'])) {
            $component = $component->options($definition['options']);
        }

        if (isset($definition['searchable']) && $definition['searchable']) {
            $component = $component->searchable();
        }

        if (isset($definition['multiple']) && $definition['multiple']) {
            $component = $component->multiple();
        }

        return $component;
    }

    /**
     * Create font picker component
     */
    protected function createFontPicker(string $key, string $label, array $definition): FontPicker
    {
        return FontPicker::make($this->getThemeFieldKey($key))
            ->label($label);
    }

    /**
     * Generate the settings key for theme fields
     */
    public function getThemeFieldKey(string $fieldKey): string
    {
        $activeTheme = $this->themeService->getActiveTheme();
        return "theme_{$activeTheme}_{$fieldKey}";
    }

    public function extractFieldKey(string $settingKey): ?string
    {
        $activeTheme = $this->themeService->getActiveTheme();
        return str($settingKey)->after("theme_{$activeTheme}_")->value();
    }

    /**
     * Get all theme field form components for active theme
     */
    public function getThemeFormComponents(): array
    {
        $fields = $this->getActiveThemeFields();
        $components = [];

        foreach ($fields as $fieldDefinition) {
            $components[] = $this->createFormComponent($fieldDefinition);
        }

        return $components;
    }

    /**
     * Get theme field value
     */
    public function getThemeFieldValue(string $fieldKey): mixed
    {
        $settingKey = $this->getThemeFieldKey($fieldKey);

        try {
            // Try to get from existing SiteSettings enum
            return SiteSettings::from($settingKey)->get();
        } catch (\ValueError) {
            // If not in enum, get from settings directly
            return settings($settingKey);
        }
    }

    public function getThemeFieldDefaultValue(string $fieldKey): mixed
    {
        return collect($this->getActiveThemeFields())
                   ->filter(fn($field) => $field['key'] === $this->extractFieldKey($fieldKey))
                   ->first()['default'] ?? null;
    }

    /**
     * Set theme field value
     */
    public function setThemeFieldValue(string $fieldKey, mixed $value): void
    {
        $settingKey = $this->getThemeFieldKey($fieldKey);

        try {
            // Try to set via SiteSettings enum
            SiteSettings::from($settingKey)->set($value);
        } catch (\ValueError) {
            // If not in enum, set directly
            settings()->set($settingKey, $value);
        }
    }

    /**
     * Get all theme field values for active theme
     */
    public function getAllThemeFieldValues(): array
    {
        $fields = $this->getActiveThemeFields();
        $values = [];

        foreach ($fields as $fieldDefinition) {
            $fieldKey = $fieldDefinition['key'] ?? '';
            if ($fieldKey) {
                $values[$fieldKey] = $this->getThemeFieldValue($fieldKey);
            }
        }

        return $values;
    }

    /**
     * Apply theme field values to CSS variables or other systems
     */
    public function applyThemeFieldValues(): array
    {
        $fields = $this->getActiveThemeFields();
        $cssVariables = [];

        foreach ($fields as $fieldDefinition) {
            $fieldKey = $fieldDefinition['key'] ?? '';
            $cssVar = $fieldDefinition['cssVariable'] ?? null;

            if ($fieldKey && $cssVar) {
                $value = $this->getThemeFieldValue($fieldKey);
                if ($value !== null) {
                    $cssVariables[$cssVar] = $value;
                }
            }
        }

        return $cssVariables;
    }
}
