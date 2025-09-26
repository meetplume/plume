<?php

namespace App\Support;

use Phiki\Phiki;
use App\Services\CodeThemeService;
use Phiki\Adapters\CommonMark\PhikiExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;

class CommentMarkdownExtensions
{
    public static function get(): array
    {
        $phiki = (new Phiki);

        $theme = CodeThemeService::getCodeTheme();
        $phikiTheme = CodeThemeService::getPhikiTheme($theme);

        if (CodeThemeService::isThemeCustom()) {
            $phiki->theme($theme->name, $theme->path());
        }

        return [
            new PhikiExtension(
                theme: $phikiTheme,
                phiki: $phiki,
            ),
            new ExternalLinkExtension(),
        ];
    }
}
