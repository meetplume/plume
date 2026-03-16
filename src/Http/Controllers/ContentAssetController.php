<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Meetplume\Plume\Plume;
use Meetplume\Plume\Vault;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContentAssetController
{
    /**
     * Serves static assets (images, diagrams, etc.) from a vault's content directory.
     *
     * Uses realpath() to resolve symlinks and ".." segments, then verifies the
     * resolved path sits underneath the vault's content root. This prevents
     * directory-traversal attacks (e.g. ../../../../.env).
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        /** @var string $vaultPrefix */
        $vaultPrefix = $request->route()->defaults['vaultPrefix'];

        /** @var ?Vault $vault */
        $vault = app(Plume::class)->getVault($vaultPrefix);

        abort_unless($vault !== null, 404);

        $relativePath = $request->route('path');

        $contentRoot = realpath($vault->getAbsolutePath());
        abort_unless($contentRoot !== false, 404);

        $filePath = realpath($contentRoot.'/'.$relativePath);

        abort_unless(
            $filePath !== false
            && str_starts_with($filePath, $contentRoot.DIRECTORY_SEPARATOR),
            404,
        );

        abort_if(str_ends_with($filePath, '.md'), 404);

        abort_unless(is_file($filePath), 404);

        return response()->file($filePath);
    }
}
