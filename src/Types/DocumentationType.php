<?php

declare(strict_types=1);

namespace Meetplume\Plume\Types;

use Meetplume\Plume\Collection;

class DocumentationType implements CollectionType
{
    public function indexComponent(): string
    {
        return 'plume/documentation/index';
    }

    public function showComponent(): string
    {
        return 'plume/documentation/show';
    }

    /**
     * @return array<string, mixed>
     */
    public function indexProps(Collection $collection): array
    {
        return [
            'collection' => $collection->toArray(),
            'pages' => $collection->discoverFiles(),
            'navigation' => $collection->readNavigation(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function showProps(Collection $collection, string $slug): array
    {
        return [
            'collection' => $collection->toArray(),
            'pages' => $collection->discoverFiles(),
            'currentSlug' => $slug,
            'navigation' => $collection->readNavigation(),
        ];
    }
}
