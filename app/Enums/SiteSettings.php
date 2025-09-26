<?php

namespace App\Enums;

use Exception;
use Rawilk\Settings\Support\Context;
use Rawilk\Settings\Facades\Settings;

enum SiteSettings: string
{
    case POST_DEFAULT_IMAGE = 'post_default_image';
    case SITE_LOGO = 'site_logo';
    case FAVICON = 'favicon';
    case SITE_NAME = 'site_name';
    case DISPLAY_SITE_NAME = 'display_site_name';
    case PRIMARY_COLOR = 'primary_color';
    case NEUTRAL_COLOR = 'neutral_color';
    case HEADING_FONT = 'heading_font';
    case BODY_FONT = 'body_font';
    case CODE_FONT = 'code_font';
    case CODE_THEME = 'code_theme';
    case HERO_TITLE = 'hero_title';
    case HERO_SUBTITLE = 'hero_subtitle';
    case HERO_IMAGE = 'hero_image';
    case HERO_IMAGE_HEIGHT = 'hero_image_height';
    case HERO_IMAGE_FULL_WIDTH = 'hero_image_full_width';
    case ABOUT_IMAGE = 'about_image';
    case ABOUT_TEXT = 'about_text';
    case ABOUT_TITLE = 'about_title';
    case ABOUT_IMAGE_CIRCULAR = 'about_image_circular';
    case ABOUT_IMAGE_WIDTH = 'about_image_width';
    case ABOUT_IMAGE_HEIGHT = 'about_image_height';
    case CONTACT_EMAIL = 'contact_email';
    case FOOTER_TEXT = 'footer_text';
    case COPYRIGHT_TEXT = 'copyright_text';
    case MAIN_MENU = 'main_menu';
    case MAIN_MENU_MORE = 'main_menu_more';
    case FOOTER_MENU = 'footer_menu';
    case PERMALINKS = 'permalinks';
    case LANGUAGES = 'languages';
    case DEFAULT_LANGUAGE = 'default_language';
    case FALLBACK_LANGUAGE = 'fallback_language';
    case MAIL_MAILER = 'mail_mailer';
    case MAIL_HOST = 'mail_host';
    case MAIL_PORT = 'mail_port';
    case MAIL_USERNAME = 'mail_username';
    case MAIL_PASSWORD = 'mail_password';
    case MAIL_ENCRYPTION = 'mail_encryption';
    case MAIL_FROM_ADDRESS = 'mail_from_address';
    case MAIL_FROM_NAME = 'mail_from_name';
    case QUEUE_CONNECTION = 'queue_connection';
    case DARK_MODE = 'dark_mode';
    case SOCIALS = 'socials';
    case ACTIVE_THEME = 'active_theme';
    case THEME_CUSTOM_CSS = 'theme_custom_css';
    case SOCIALS_ICON_FILL = 'socials_icon_fill';

    /**
     * Get the default value for the setting
     *
     * @noinspection PhpDuplicateMatchArmBodyInspection
     */
    public function getDefaultValue(): string|array|null
    {
        return match ($this) {
            self::POST_DEFAULT_IMAGE => null,
            self::SITE_LOGO => null,
            self::FAVICON => null,
            self::SITE_NAME => config('app.name'),
            self::DISPLAY_SITE_NAME => true,
            self::PRIMARY_COLOR => '#6366f1',
            self::NEUTRAL_COLOR => 'neutral',
            self::HEADING_FONT => 'Hanken Grotesk',
            self::BODY_FONT => 'Hanken Grotesk',
            self::CODE_FONT => 'JetBrains Mono',
            self::CODE_THEME => 'catppuccin-macchiato',
            self::HERO_TITLE => "Welcome to my |personal| blog",
            self::HERO_SUBTITLE => "A place to share my thoughts",
            self::HERO_IMAGE => null,
            self::HERO_IMAGE_HEIGHT => 350,
            self::HERO_IMAGE_FULL_WIDTH => true,
            self::ABOUT_IMAGE => null,
            self::ABOUT_TEXT => null,
            self::ABOUT_TITLE => "About me",
            self::ABOUT_IMAGE_CIRCULAR => true,
            self::ABOUT_IMAGE_WIDTH => 100,
            self::ABOUT_IMAGE_HEIGHT => 100,
            self::CONTACT_EMAIL => '',
            self::FOOTER_TEXT => "Made with ❤️ by me",
            self::COPYRIGHT_TEXT => "©{year}",
            self::MAIN_MENU => [
                [
                    "icon" => "o-home",
                    "name" => "Home",
                    "url" => "/",
                    "open_in_new_tab" => false,
                    "page" => "home",
                ],
                [
                    "icon" => "o-newspaper",
                    "name" => "Blog",
                    "url" => "/blog",
                    "open_in_new_tab" => false,
                    "page" => "blog",
                ],
                [
                    "icon" => "o-user",
                    "name" => null,
                    "url" => null,
                    "open_in_new_tab" => false,
                    "page" => "user-account", // Custom component "User account dropdown"
                ],
            ],
            self::MAIN_MENU_MORE => [
                [
                    "type" => "item",
                    "data" => [
                        "icon" => "iconoir-chat-bubble-question",
                        "page" => "custom",
                        "name" => "About",
                        "url" => "/#about",
                        "open_in_new_tab" => false,
                    ],
                ],
                [
                    "type" => "item",
                    "data" => [
                        "icon" => "iconoir-mail-out",
                        "page" => "custom",
                        "name" => "Contact",
                        "url" => "mailto:hello@example.com",
                        "open_in_new_tab" => false,
                    ],
                ],
                [
                    "type" => "divider",
                    "data" => [
                        "label" => "Source Code",
                    ],
                ],
                [
                    "type" => "item",
                    "data" => [
                        "icon" => "iconoir-git-fork",
                        "page" => "custom",
                        "name" => "Source code",
                        "url" => "https://github.com/charlieetienne/blog",
                        "open_in_new_tab" => true,
                    ],
                ],
                [
                    "type" => "divider",
                    "data" => [
                        "label" => "Follow me",
                    ],
                ],
                [
                    "type" => "item",
                    "data" => [
                        "icon" => "iconoir-github",
                        "page" => "custom",
                        "name" => "GitHub",
                        "url" => "https://github.com/charlieetienne",
                        "open_in_new_tab" => true,
                    ],
                ],
                [
                    "type" => "item",
                    "data" => [
                        "icon" => "iconoir-x",
                        "page" => "custom",
                        "name" => "X",
                        "url" => "https://x.com/charlieetienne",
                        "open_in_new_tab" => true,
                    ],
                ],
            ],
            self::FOOTER_MENU => [
                [
                    "name" => "Home",
                    "url" => "/",
                    "open_in_new_tab" => false,
                    "page" => "home",
                ],
                [
                    "name" => "Blog",
                    "url" => "/blog",
                    "open_in_new_tab" => false,
                    "page" => "blog",
                ],
                [
                    "name" => "About",
                    "url" => "/#about",
                    "open_in_new_tab" => false,
                    "page" => "custom",
                ],
                [
                    "name" => "Contact",
                    "url" => "mailto:hello@example.com",
                    "open_in_new_tab" => false,
                    "page" => "custom",
                ],
            ],
            self::PERMALINKS => collect(MainPages::cases())->mapWithKeys(fn(MainPages $page) => [$page->value => $page->getDefaultSlug()])->toArray(),
            self::LANGUAGES => ['en'],
            self::DEFAULT_LANGUAGE => 'en',
            self::FALLBACK_LANGUAGE => 'en',
            self::MAIL_MAILER => config('mail.default'),
            self::MAIL_HOST => config('mail.mailers.smtp.host'),
            self::MAIL_PORT => config('mail.mailers.smtp.port'),
            self::MAIL_USERNAME => config('mail.mailers.smtp.username'),
            self::MAIL_PASSWORD => config('mail.mailers.smtp.password'),
            self::MAIL_ENCRYPTION => config('mail.mailers.smtp.encryption'),
            self::MAIL_FROM_ADDRESS => config('mail.from.address'),
            self::MAIL_FROM_NAME => config('mail.from.name'),
            self::QUEUE_CONNECTION => 'sync',
            self::DARK_MODE => 'switcher',
            self::SOCIALS => [],
            self::SOCIALS_ICON_FILL => false,
            self::ACTIVE_THEME => 'default',
            self::THEME_CUSTOM_CSS => null,
        };
    }

    public function getThemeSettingOrDefaultValue(): mixed
    {
        if (in_array($this, self::canBeOverridenByTheme())) {
            return theme()->settings()[$this->value] ?? $this->getDefaultValue();
        }
        return $this->getDefaultValue();
    }

    public function get(array $context = []): mixed
    {
        try{
            if ($this->is_translatable() && empty($context)) {
                $context = ['language' => app()->getLocale() ?? 'en'];
            }
            return Settings::context(new Context($context))->get($this->value, $this->getThemeSettingOrDefaultValue());
        }
        catch (Exception){
            return $this->getThemeSettingOrDefaultValue();
        }
    }

    public function set(mixed $value, array $context = []): void
    {
        Settings::context(new Context($context))->set($this->value, $value);
    }

    public static function translatable(): array
    {
        $translatable = [
            self::HERO_TITLE,
            self::HERO_SUBTITLE,
            self::ABOUT_TEXT,
            self::ABOUT_TITLE,
            self::FOOTER_TEXT,
            self::COPYRIGHT_TEXT,
        ];

        return array_map(fn(self $setting) => $setting->value, $translatable);
    }

    public static function canBeOverridenByTheme(): array
    {
        return [
            self::PRIMARY_COLOR,
            self::NEUTRAL_COLOR,
            self::HEADING_FONT,
            self::BODY_FONT,
            self::CODE_FONT,
            self::CODE_THEME,
            self::HERO_IMAGE_HEIGHT,
            self::HERO_IMAGE_FULL_WIDTH,
            self::ABOUT_IMAGE_CIRCULAR,
            self::ABOUT_IMAGE_WIDTH,
            self::ABOUT_IMAGE_HEIGHT,
            self::DARK_MODE,
        ];
    }

    public function is_translatable(): bool
    {
        return in_array($this->value, self::translatable());
    }
}
