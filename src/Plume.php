<?php

declare(strict_types=1);

namespace Meetplume\Plume;

class Plume
{
    private ?PlumeConfiguration $configuration = null;

    /**
     * Start fluent configuration.
     */
    public function configure(): PlumeConfiguration
    {
        if (! $this->configuration instanceof PlumeConfiguration) {
            $this->configuration = new PlumeConfiguration;
        }

        return $this->configuration;
    }

    /**
     * Get the current configuration.
     */
    public function getConfiguration(): ?PlumeConfiguration
    {
        return $this->configuration;
    }

    /**
     * Get a vault by prefix.
     */
    public function getVault(string $prefix): ?Vault
    {
        return $this->configuration?->getVault($prefix);
    }
}
