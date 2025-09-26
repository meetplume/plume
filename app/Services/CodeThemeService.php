<?php

namespace App\Services;

use Phiki\Phiki;
use Phiki\Theme\Theme;
use App\Enums\CodeTheme;
use Phiki\Grammar\Grammar;
use App\Enums\SiteSettings;

class CodeThemeService
{
    public static function getCodeTheme(): Theme|CodeTheme|null
    {
        return filled(SiteSettings::CODE_THEME->get()) ?
            CodeTheme::tryFrom(SiteSettings::CODE_THEME->get()) ??
            Theme::tryFrom(SiteSettings::CODE_THEME->get()) : Theme::CatppuccinMacchiato;
    }

    public static function isThemeCustom(): bool
    {
        return self::getCodeTheme() instanceof CodeTheme;
    }

    public static function getPhikiTheme(Theme|CodeTheme $theme): Theme|string
    {
        if (CodeThemeService::isThemeCustom()) {
            return $theme->name;
        }
        return $theme;
    }

    public static function codeToHtml(string $code, Grammar $grammar, Theme|CodeTheme $theme): string
    {
        $phiki = (new Phiki);

        if (CodeThemeService::isThemeCustom()) {
            $phiki->theme($theme->name, $theme->path());
        }

        $phikiTheme = CodeThemeService::getPhikiTheme($theme);

        return '<div class="not-prose phiki-wrapper-main">'.$phiki->codeToHtml($code, $grammar, $phikiTheme).'</div>';
    }
}
