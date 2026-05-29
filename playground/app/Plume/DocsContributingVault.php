<?php

declare(strict_types=1);

namespace App\Plume;

use Illuminate\Http\Request;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

class DocsContributingVault extends Vault
{
    protected string $prefix = '/docs-contributing';

    protected string $path = 'vendor/meetplume/plume/docs-contributing';

    protected string $layout = 'docs';

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
                    Page::make('overview')->label('Overview'),
                    Page::make('getting-started/introduction')->label('Introduction'),
                    Page::make('getting-started/development-workflow')->label('Development Workflow'),
                    Page::make('getting-started/playground')->label('Playground'),
                ]),

            NavGroup::make('architecture')
                ->icon('layers')
                ->pages([
                    Page::make('architecture/php-backend')->label('PHP Backend'),
                    Page::make('architecture/frontend')->label('Frontend Pipeline'),
                ]),

            NavGroup::make('authoring')
                ->icon('notebook-pen')
                ->pages([
                    Page::make('authoring/callouts')->label('Callouts'),
                ]),

            NavGroup::make('reference')
                ->icon('book')
                ->pages([
                    Page::make('roadmap')->label('Roadmap'),
                    Page::make('adr/001-markdown-pages-blocks-system')->label('ADR-001: Markdown pages & blocks'),
                ]),
        ];
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
