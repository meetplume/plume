<?php

declare(strict_types=1);

namespace Meetplume\Plume\Enums;

/**
 * An entry of the page menu, next to the page title.
 */
enum PageAction: string
{
    /**
     * Copy the page's Markdown to the clipboard, to paste it into an AI assistant.
     */
    case Copy = 'copy';

    /**
     * Open the page's Markdown, served at the page's address followed by `.md`.
     */
    case ViewMarkdown = 'markdown';

    /**
     * Open ChatGPT, asked to read the page's Markdown address.
     */
    case OpenInChatGpt = 'chatgpt';

    /**
     * Open Claude, asked to read the page's Markdown address.
     */
    case OpenInClaude = 'claude';

    /**
     * Print the page, or save it as a PDF, from the browser's print dialog.
     */
    case ExportPdf = 'pdf';

    /**
     * The actions that keep the page on the site: none hands its address to
     * another service, which could not read a vault behind a login anyway.
     *
     * @return array<int, self>
     */
    public static function internal(): array
    {
        return array_values(array_filter(self::cases(), fn (self $action): bool => ! $action->isExternal()));
    }

    public function isExternal(): bool
    {
        return match ($this) {
            self::OpenInChatGpt, self::OpenInClaude => true,
            default => false,
        };
    }
}
