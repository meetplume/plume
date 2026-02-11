<?php

declare(strict_types=1);

namespace Meetplume\Plume;

class CollectionRegistry
{
    /**
     * @var array<string, Collection>
     */
    private array $collections = [];

    public function register(Collection $collection): void
    {
        $this->collections[$collection->getPrefix()] = $collection;
    }

    public function resolve(string $prefix): ?Collection
    {
        return $this->collections[$prefix] ?? null;
    }

    /**
     * @return array<string, Collection>
     */
    public function all(): array
    {
        return $this->collections;
    }
}
