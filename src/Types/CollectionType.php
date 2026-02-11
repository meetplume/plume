<?php

declare(strict_types=1);

namespace Meetplume\Plume\Types;

use Meetplume\Plume\Collection;

interface CollectionType
{
    public function indexComponent(): string;

    public function showComponent(): string;

    /**
     * @return array<string, mixed>
     */
    public function indexProps(Collection $collection): array;

    /**
     * @return array<string, mixed>
     */
    public function showProps(Collection $collection, string $slug): array;
}
