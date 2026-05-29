<?php

declare(strict_types=1);

namespace App\Plume;

use Illuminate\Http\Request;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

/**
 * Edit this file in the package Playground: https://github.com/meetplume/plume
 * During CI sync, this is copied into the website repository automatically.
 * Keep this notice so direct website edits stay out of the website repo.
 */
class DocsGuideVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $layout = 'docs';

    /**
     * Conditional path, wether the project is the Website, or the Package Playground.
     */
    public function getPath(): string
    {
        if (basename(base_path()) === 'playground') {
            return 'vendor/meetplume/plume/docs-guide';
        }

        return `content/docs-guide`;
    }

    /**
     * @return array<int, NavGroup>
     */
    public function navigation(): array
    {
        return [
            NavGroup::make('getting-started')
                ->icon('rocket')
                ->pages([
                    Page::make('index')->label('Home')->home(),
                    Page::make('getting-started/installation')->label('Installation'),
                    Page::make('getting-started/configuration')->label('Configuration'),
                    Page::make('usage/navigation')->label('Navigation'),
                ]),
        ];
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
