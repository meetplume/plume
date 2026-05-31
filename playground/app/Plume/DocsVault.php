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
class DocsVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $layout = 'docs';

    /**
     * Conditional path, wether the project is the Website, or the Package Playground.
     */
    public function getPath(): string
    {
        if (basename(base_path()) === 'playground') {
            return 'vendor/meetplume/plume/docs';
        }

        return 'content/docs';
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
                    Page::make('getting-started/quick-start')->label('Quick Start'),
                ]),

            NavGroup::make('vaults')
                ->icon('database')
                ->pages([
                    Page::make('vaults/overview')->label('Overview'),
                    Page::make('vaults/discovery')->label('Discovery Modes'),
                    Page::make('vaults/access-control')->label('Access Control'),
                ]),

            NavGroup::make('navigation')
                ->icon('list-tree')
                ->pages([
                    Page::make('navigation/sidebar')->label('Sidebar'),
                    Page::make('navigation/pages')->label('Pages'),
                    Page::make('navigation/tabs')->label('Tabs'),
                    Page::make('navigation/versions')->label('Versions'),
                    Page::make('navigation/languages')->label('Languages'),
                ]),

            NavGroup::make('authoring')
                ->icon('notebook-pen')
                ->pages([
                    Page::make('authoring/frontmatter')->label('Frontmatter'),
                    Page::make('authoring/callouts')->label('Callouts'),
                    Page::make('authoring/landing-pages')->label('Landing Pages'),
                    Page::make('authoring/assets')->label('Images & Assets'),
                ]),

            NavGroup::make('appearance')
                ->icon('palette')
                ->pages([
                    Page::make('appearance/theming')->label('Theming'),
                    Page::make('appearance/presets')->label('Theme Presets'),
                    Page::make('appearance/layouts')->label('Layouts'),
                ]),

            NavGroup::make('site')
                ->icon('layout-template')
                ->pages([
                    Page::make('site/header')->label('Header'),
                    Page::make('site/footer')->label('Footer'),
                    Page::make('site/branding')->label('Branding'),
                ]),

            NavGroup::make('reference')
                ->icon('book')
                ->pages([
                    Page::make('reference/commands')->label('Artisan Commands'),
                    Page::make('reference/search')->label('Search'),
                ]),
        ];
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
