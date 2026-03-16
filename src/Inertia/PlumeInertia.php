<?php

declare(strict_types=1);

namespace Meetplume\Plume\Inertia;

class PlumeInertia
{
    private string $rootView = 'plume::app';

    /** @var array<string, mixed> */
    private array $sharedProps = [];

    public function setRootView(string $view): void
    {
        $this->rootView = $view;
    }

    public function getRootView(): string
    {
        return $this->rootView;
    }

    public function share(string $key, mixed $value): void
    {
        $this->sharedProps[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getShared(): array
    {
        return $this->sharedProps;
    }

    /**
     * @param  array<string, mixed>  $props
     */
    public function render(string $component, array $props = []): PlumeInertiaResponse
    {
        return new PlumeInertiaResponse(
            component: $component,
            props: array_merge($this->sharedProps, $props),
            rootView: $this->rootView,
        );
    }
}
