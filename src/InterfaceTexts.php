<?php

declare(strict_types=1);

namespace Meetplume\Plume;

/**
 * The words of Plume's own interface (search, table of contents, menus),
 * translated in the locale of the page through Laravel's JSON translations:
 * Plume ships `lang/{locale}.json`, and the host app overrides any key in its
 * own `lang/{locale}.json`.
 */
final class InterfaceTexts
{
    private const array KEYS = [
        'Search…',
        'Search documentation',
        'Search documentation…',
        'Preparing the search index…',
        'Could not load the search index. Try refreshing the page.',
        'No results for',
        'Other',
        'to navigate',
        'to open',
        'to close',
        'On this page',
        'Table of contents',
        'Toggle dark mode',
        'Language',
        'Version',
        'Open menu',
        'Close menu',
        'Powered by',
        'Details',
        'Copy to clipboard',
        'Copied!',
        'Previous',
        'Next',
        'Copy page',
        'More page actions',
        'Page',
        'Copy page as Markdown for LLMs',
        'View as Markdown',
        'View this page as plain text',
        'Open in ChatGPT',
        'Ask ChatGPT about this page',
        'Open in Claude',
        'Ask Claude about this page',
        'Export as PDF',
        'Print this page or save it as a PDF',
        'Read :url and answer questions about the content.',
    ];

    /**
     * @return array<string, string>
     */
    public static function for(string $locale): array
    {
        $texts = [];

        foreach (self::KEYS as $key) {
            $translation = trans()->get($key, [], $locale);
            $texts[$key] = is_string($translation) ? $translation : $key;
        }

        return $texts;
    }
}
