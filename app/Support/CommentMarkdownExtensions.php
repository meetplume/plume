<?php

namespace App\Support;

use Phiki\Theme\Theme;
use App\Enums\SiteSettings;
use Phiki\CommonMark\PhikiExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;

class CommentMarkdownExtensions
{
    public static function get(): array
    {
        return [
            new PhikiExtension(filled(SiteSettings::CODE_THEME->get()) ? Theme::tryFrom(SiteSettings::CODE_THEME->get()) : Theme::CatppuccinMacchiato, withWrapper: true),
            new ExternalLinkExtension(),
        ];
    }
}
