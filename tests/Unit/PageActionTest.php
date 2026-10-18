<?php

declare(strict_types=1);

use Meetplume\Plume\Enums\PageAction;
use Meetplume\Plume\Vault;

it('offers every page action by default', function (): void {
    expect((new Vault)->pageActions())->toBe(PageAction::cases());
});

it('keeps the actions that send the page address to another service out of the internal ones', function (): void {
    expect(PageAction::internal())->toBe([PageAction::Copy, PageAction::ViewMarkdown, PageAction::ExportPdf])
        ->and(PageAction::OpenInChatGpt->isExternal())->toBeTrue()
        ->and(PageAction::OpenInClaude->isExternal())->toBeTrue();
});
