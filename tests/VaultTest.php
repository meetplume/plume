<?php

use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Tab;
use Meetplume\Plume\Vault;
use Meetplume\Plume\Version;

function makeTestVault(): Vault
{
    return new class extends Vault
    {
        protected string $prefix = '/docs';

        protected string $path = '';

        protected string $layout = 'docs';

        public function navigation(): array
        {
            return [
                NavGroup::make('getting-started')
                    ->pages([
                        Page::make('intro'),
                        Page::make('quickstart'),
                    ]),
                NavGroup::make('advanced')
                    ->pages([
                        Page::make('config'),
                    ]),
            ];
        }
    };
}

it('has default layout of page', function (): void {
    $vault = new Vault;

    expect($vault->getLayout())->toBe('page');
});

it('detects when navigation is defined', function (): void {
    $vault = makeTestVault();

    expect($vault->hasNavigation())->toBeTrue();
});

it('detects when navigation is not defined', function (): void {
    $vault = new Vault;

    expect($vault->hasNavigation())->toBeFalse();
});

it('detects when tabs are defined', function (): void {
    $vault = new class extends Vault
    {
        public function tabs(): array
        {
            return [Tab::make('docs')];
        }
    };

    expect($vault->hasTabs())->toBeTrue();
});

it('detects when versions are defined', function (): void {
    $vault = new class extends Vault
    {
        public function versions(): array
        {
            return [Version::make('v2')->default()];
        }
    };

    expect($vault->hasVersions())->toBeTrue();
});

it('resolves pages from navigation', function (): void {
    $vault = makeTestVault();
    $pages = $vault->resolvePages();

    expect($pages)->toHaveCount(3)
        ->and(array_keys($pages))->toBe(['intro', 'quickstart', 'config']);
});

it('resolves pages from tabs', function (): void {
    $vault = new class extends Vault
    {
        public function tabs(): array
        {
            return [
                Tab::make('docs')->groups([
                    NavGroup::make('start')->pages([Page::make('intro')]),
                ]),
                Tab::make('api')->groups([
                    NavGroup::make('endpoints')->pages([Page::make('users')]),
                ]),
            ];
        }
    };

    $pages = $vault->resolvePages(tab: 'docs');
    expect($pages)->toHaveCount(1)
        ->and(array_keys($pages))->toBe(['intro']);

    $pages = $vault->resolvePages(tab: 'api');
    expect($pages)->toHaveCount(1)
        ->and(array_keys($pages))->toBe(['users']);
});

it('resolves navigation from version-specific tabs', function (): void {
    $vault = new class extends Vault
    {
        public function versions(): array
        {
            return [
                Version::make('v2')->default()->tabs([
                    Tab::make('docs')->groups([
                        NavGroup::make('start')->pages([Page::make('intro-v2')]),
                    ]),
                ]),
                Version::make('v1')->tabs([
                    Tab::make('docs')->groups([
                        NavGroup::make('start')->pages([Page::make('intro-v1')]),
                    ]),
                ]),
            ];
        }
    };

    $groups = $vault->resolveNavigation(version: 'v2', tab: 'docs');
    expect($groups[0]->getPages()[0]->key)->toBe('intro-v2');

    $groups = $vault->resolveNavigation(version: 'v1', tab: 'docs');
    expect($groups[0]->getPages()[0]->key)->toBe('intro-v1');
});

it('falls back to vault tabs when version has no tabs', function (): void {
    $vault = new class extends Vault
    {
        public function versions(): array
        {
            return [
                Version::make('v2')->default(),
                Version::make('v1'),
            ];
        }

        public function tabs(): array
        {
            return [
                Tab::make('docs')->groups([
                    NavGroup::make('start')->pages([Page::make('shared-intro')]),
                ]),
            ];
        }
    };

    $groups = $vault->resolveNavigation(version: 'v2', tab: 'docs');
    expect($groups[0]->getPages()[0]->key)->toBe('shared-intro');
});

it('resolves file path without language or version', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = '/tmp/test-content';
    };

    $page = Page::make('intro');

    expect($vault->resolveFilePath($page))->toBe('/tmp/test-content/intro.md');
});

it('resolves file path with language prefix', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = '/tmp/test-content';
    };

    $page = Page::make('intro');

    expect($vault->resolveFilePath($page, language: 'pt'))->toBe('/tmp/test-content/pt/intro.md');
});

it('resolves file path with version prefix', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = '/tmp/test-content';
    };

    $page = Page::make('intro');

    expect($vault->resolveFilePath($page, version: 'v2'))->toBe('/tmp/test-content/v2/intro.md');
});

it('resolves file path with language and version', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = '/tmp/test-content';
    };

    $page = Page::make('intro');

    expect($vault->resolveFilePath($page, language: 'pt', version: 'v2'))->toBe('/tmp/test-content/pt/v2/intro.md');
});

it('collects all slugs across navigation', function (): void {
    $vault = makeTestVault();

    $slugs = $vault->collectAllSlugs();

    expect($slugs)->toBe(['intro', 'quickstart', 'config']);
});

it('collects all slugs across tabs', function (): void {
    $vault = new class extends Vault
    {
        public function tabs(): array
        {
            return [
                Tab::make('docs')->groups([
                    NavGroup::make('start')->pages([Page::make('intro')]),
                ]),
                Tab::make('api')->groups([
                    NavGroup::make('endpoints')->pages([Page::make('users')]),
                ]),
            ];
        }
    };

    $slugs = $vault->collectAllSlugs();

    expect($slugs)->toContain('intro', 'users');
});

it('gets default version', function (): void {
    $vault = new class extends Vault
    {
        public function versions(): array
        {
            return [
                Version::make('v2')->default(),
                Version::make('v1'),
            ];
        }
    };

    expect($vault->getDefaultVersion()?->key)->toBe('v2');
});

it('returns first version when no default', function (): void {
    $vault = new class extends Vault
    {
        public function versions(): array
        {
            return [
                Version::make('v2'),
                Version::make('v1'),
            ];
        }
    };

    expect($vault->getDefaultVersion()?->key)->toBe('v2');
});

it('has manual discovery by default', function (): void {
    $vault = new Vault;

    expect($vault->getDiscovery())->toBe(Discovery::Manual);
});

it('includes pages() in collectAllSlugs for manual mode', function (): void {
    $vault = new class extends Vault
    {
        public function navigation(): array
        {
            return [
                NavGroup::make('main')->pages([
                    Page::make('intro'),
                ]),
            ];
        }

        public function pages(): array
        {
            return [
                Page::make('changelog'),
                Page::make('license'),
            ];
        }
    };

    $slugs = $vault->collectAllSlugs();

    expect($slugs)->toContain('intro', 'changelog', 'license');
});

it('resolves pages includes pages() entries', function (): void {
    $vault = new class extends Vault
    {
        public function navigation(): array
        {
            return [
                NavGroup::make('main')->pages([
                    Page::make('intro'),
                ]),
            ];
        }

        public function pages(): array
        {
            return [
                Page::make('changelog'),
            ];
        }
    };

    $pages = $vault->resolvePages();

    expect($pages)->toHaveCount(2)
        ->and(array_keys($pages))->toContain('intro', 'changelog');
});

it('navigation pages take precedence over pages()', function (): void {
    $vault = new class extends Vault
    {
        public function navigation(): array
        {
            return [
                NavGroup::make('main')->pages([
                    Page::make('intro')->label('Nav Label'),
                ]),
            ];
        }

        public function pages(): array
        {
            return [
                Page::make('intro')->label('Pages Label'),
            ];
        }
    };

    $pages = $vault->resolvePages();

    expect($pages['intro']->getLabel())->toBe('Nav Label');
});

it('detects when pages are defined', function (): void {
    $vault = new class extends Vault
    {
        public function pages(): array
        {
            return [Page::make('extra')];
        }
    };

    expect($vault->hasPages())->toBeTrue();
});

it('detects when pages are not defined', function (): void {
    $vault = new Vault;

    expect($vault->hasPages())->toBeFalse();
});

it('collects slugs from filesystem for mapped mode', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = __DIR__.'/fixtures/scanner';

        protected Discovery $discovery = Discovery::Mapped;

        public function navigation(): array
        {
            return [
                NavGroup::make('main')->pages([
                    Page::make('manual-page'),
                ]),
            ];
        }
    };

    $slugs = $vault->collectAllSlugs();

    expect($slugs)->toContain('/', 'config', 'manual-page');
});

it('collects slugs from filesystem for auto mode', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = __DIR__.'/fixtures/scanner';

        protected Discovery $discovery = Discovery::Auto;
    };

    $slugs = $vault->collectAllSlugs();

    expect($slugs)->toContain('/', 'config')
        ->and($slugs)->toHaveCount(4);
});

it('auto mode derives navigation from filesystem', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = __DIR__.'/fixtures/scanner';

        protected Discovery $discovery = Discovery::Auto;
    };

    $groups = $vault->resolveNavigation();
    $keys = array_map(fn (NavGroup $g): string => $g->key, $groups);

    expect($keys)->toContain('_root', 'getting-started', 'advanced');
});

it('mapped mode uses navigation() for sidebar', function (): void {
    $vault = new class extends Vault
    {
        protected string $path = __DIR__.'/fixtures/scanner';

        protected Discovery $discovery = Discovery::Mapped;

        public function navigation(): array
        {
            return [
                NavGroup::make('custom')->pages([
                    Page::make('intro')->label('Custom Intro'),
                ]),
            ];
        }
    };

    $groups = $vault->resolveNavigation();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]->key)->toBe('custom');
});
