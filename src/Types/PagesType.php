<?php

declare(strict_types=1);

namespace Meetplume\Plume\Types;

use Meetplume\Plume\Collection;

class PagesType implements CollectionType
{
    public function indexComponent(): string
    {
        return 'plume/pages/index';
    }

    public function showComponent(): string
    {
        return 'plume/pages/show';
    }

    /**
     * @return array<string, mixed>
     */
    public function indexProps(Collection $collection): array
    {
        return [
            'collection' => $collection->toArray(),
            'pages' => $collection->discoverFiles(),
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
        ];
    }
}
