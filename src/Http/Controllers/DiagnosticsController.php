<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Meetplume\Plume\Plume;
use Meetplume\Plume\Vault;

class DiagnosticsController
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var string $vaultPrefix */
        $vaultPrefix = $request->route()->defaults['vaultPrefix'];

        $vault = app(Plume::class)->getVault($vaultPrefix);

        abort_unless($vault !== null, 404);

        return response()->json($this->buildDiagnostics($vault));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDiagnostics(Vault $vault): array
    {
        $prefix = trim($vault->getPrefix(), '/');
        $navigationPages = $this->collectNavigationSlugs($vault);
        $allPages = $vault->resolvePages();

        $pageDetails = [];

        foreach ($allPages as $slug => $page) {
            $filePath = $vault->resolveFilePath($page);
            $pageDetails[] = [
                'key' => $page->key,
                'slug' => $page->getSlug(),
                'path' => $page->getPath(),
                'resolvedFilePath' => $filePath,
                'fileExists' => file_exists($filePath),
                'hidden' => $page->isHidden(),
                'order' => $page->getOrder(),
                'inNavigation' => in_array($slug, $navigationPages, true),
            ];
        }

        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route): bool => ($route->defaults['vaultPrefix'] ?? null) === $prefix)
            ->map(fn ($route): array => [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'methods' => $route->methods(),
            ])
            ->values()
            ->all();

        return [
            'vault' => $vault::class,
            'prefix' => $prefix,
            'path' => $vault->getPath(),
            'absolutePath' => $vault->getAbsolutePath(),
            'layout' => $vault->getLayout(),
            'discovery' => $vault->getDiscovery()->name,
            'hasNavigation' => $vault->hasNavigation(),
            'hasTabs' => $vault->hasTabs(),
            'hasVersions' => $vault->hasVersions(),
            'hasLanguages' => $vault->hasLanguages(),
            'hasPages' => $vault->hasPages(),
            'allSlugs' => $vault->collectAllSlugs(),
            'pages' => $pageDetails,
            'routes' => $routes,
        ];
    }

    /**
     * Collect slugs that appear in the navigation tree.
     *
     * @return array<int, string>
     */
    private function collectNavigationSlugs(Vault $vault): array
    {
        $slugs = [];

        foreach ($vault->resolveNavigation() as $group) {
            foreach ($group->getPages() as $page) {
                $slugs[] = $page->getSlug();
            }
        }

        return $slugs;
    }
}
