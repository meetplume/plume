<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Meetplume\Plume\Actions\SearchIndexCache;
use Meetplume\Plume\Language;
use Meetplume\Plume\Plume;
use Meetplume\Plume\Vault;
use Meetplume\Plume\Version;

class SearchIndexController
{
    public function __invoke(Request $request, SearchIndexCache $cache): Response|JsonResponse
    {
        /** @var string $vaultPrefix */
        $vaultPrefix = $request->route()->defaults['vaultPrefix'];

        $vault = app(Plume::class)->getVault($vaultPrefix);

        abort_unless($vault !== null, 404);
        abort_unless($vault->canAccess($request), 403);

        $language = $this->resolveLanguage($vault, $request->query('lang'));
        $version = $this->resolveVersion($vault, $request->query('version'));

        $index = $cache->get($vault, $language, $version);
        $etag = '"'.$index['signature'].'"';

        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'public, max-age=300, must-revalidate');
        }

        return response()
            ->json($index['records'])
            ->header('ETag', $etag)
            ->header('Cache-Control', 'public, max-age=300, must-revalidate');
    }

    private function resolveLanguage(Vault $vault, mixed $value): ?string
    {
        if (! $vault->hasLanguages()) {
            return null;
        }

        if (is_string($value) && $value !== '') {
            foreach ($vault->languages() as $language) {
                if ($language->code === $value) {
                    return $language->code;
                }
            }

            abort(400, 'Unknown language: '.$value);
        }

        $default = $vault->getDefaultLanguage();

        return $default instanceof Language ? $default->code : null;
    }

    private function resolveVersion(Vault $vault, mixed $value): ?string
    {
        if (! $vault->hasVersions()) {
            return null;
        }

        if (is_string($value) && $value !== '') {
            foreach ($vault->versions() as $version) {
                if ($version->key === $value) {
                    return $version->key;
                }
            }

            abort(400, 'Unknown version: '.$value);
        }

        $default = $vault->getDefaultVersion();

        return $default instanceof Version ? $default->key : null;
    }
}
