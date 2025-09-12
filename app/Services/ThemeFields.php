<?php

namespace App\Services;

class ThemeFields
{
    protected ThemeFieldsService $themeFieldsService;
    protected ?string $setting;
    protected ?string $themeName;

    public function __construct(ThemeFieldsService $themeFieldsService, ?string $setting = null, ?string $themeName = null)
    {
        $this->themeFieldsService = $themeFieldsService;
        $this->setting = $setting;
        $this->themeName = $themeName;
    }

    /**
     * Get all fields for the theme or the default value for a specific field
     */
    public function default(): mixed
    {
        if ($this->setting === null) {
            // Return all field defaults for the theme
            $fields = $this->getThemeFields();
            $defaults = [];
            foreach ($fields as $field) {
                if (isset($field['key'], $field['default'])) {
                    $defaults[$field['key']] = $field['default'];
                }
            }
            return $defaults;
        }

        return $this->themeFieldsService->getThemeFieldDefaultValue($this->setting);
    }

    /**
     * Get the current value for the theme field
     */
    public function value(): mixed
    {
        $fieldKey = $this->themeFieldsService->extractFieldKey($this->setting);
        return $this->themeFieldsService->getThemeFieldValue($fieldKey);
    }

    /**
     * Set the value for the theme field
     */
    public function set(mixed $value): static
    {
        $fieldKey = $this->themeFieldsService->extractFieldKey($this->setting);
        $this->themeFieldsService->setThemeFieldValue($fieldKey, $value);
        return $this;
    }

    /**
     * Get the theme field key
     */
    public function key(): string
    {
        return $this->themeFieldsService->getThemeFieldKey(
            $this->themeFieldsService->extractFieldKey($this->setting)
        );
    }

    /**
     * Get the field definition from theme configuration
     */
    public function definition(): ?array
    {
        if ($this->setting === null) {
            return null;
        }

        $fieldKey = $this->themeFieldsService->extractFieldKey($this->setting);
        $fields = $this->getThemeFields();

        return array_find($fields, fn($field) => ($field[ 'key' ] ?? '') === $fieldKey);
    }

    /**
     * Check if the field exists in the theme configuration
     */
    public function exists(): bool
    {
        return $this->definition() !== null;
    }

    /**
     * Get all fields for the current or specified theme
     */
    public function all(): array
    {
        return $this->getThemeFields();
    }

    /**
     * Get theme fields for the current or specified theme
     */
    protected function getThemeFields(): array
    {
        if ($this->themeName) {
            return $this->themeFieldsService->getThemeFields($this->themeName);
        }

        return $this->themeFieldsService->getActiveThemeFields();
    }

}
