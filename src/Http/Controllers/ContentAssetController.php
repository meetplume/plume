<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Meetplume\Plume\Collection;
use Meetplume\Plume\Plume;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContentAssetController
{
    /**
     * Serves static assets (images, diagrams, etc.) from a collection's content directory.
     *
     * Uses realpath() to resolve symlinks and ".." segments, then verifies the
     * resolved path sits underneath the collection's content root. This prevents
     * directory-traversal attacks (e.g. ../../../../.env).
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        /** @var string $collectionPrefix */
        $collectionPrefix = $request->route()->defaults['collectionPrefix'];

        /** @var Collection $collection */
        $collection = app(Plume::class)->getCollection($collectionPrefix);
        $relativePath = $request->route('path');

        // Resolve the content root to an absolute, canonical path.
        $contentRoot = realpath($collection->contentPath);
        abort_unless($contentRoot !== false, 404);

        // Resolve the requested file — realpath() collapses any ".." segments
        // and returns false when the target does not exist.
        $filePath = realpath($contentRoot.'/'.$relativePath);

        // Verify the resolved path lives inside the content root.
        // The trailing DIRECTORY_SEPARATOR prevents prefix collisions
        // (e.g. /content-secrets matching /content).
        abort_unless(
            $filePath !== false
            && str_starts_with($filePath, $contentRoot.DIRECTORY_SEPARATOR),
            404,
        );

        // Markdown source files must only be rendered through Plume, never served raw.
        abort_if(str_ends_with($filePath, '.md'), 404);

        // Only serve regular files (not directories or special entries).
        abort_unless(is_file($filePath), 404);

        return response()->file($filePath);
    }
}
