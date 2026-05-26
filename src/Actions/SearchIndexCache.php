<?php

declare(strict_types=1);

namespace Meetplume\Plume\Actions;

use Meetplume\Plume\Vault;

final class SearchIndexCache
{
    private readonly string $storageDirectory;

    public function __construct(?string $storageDirectory = null)
    {
        $this->storageDirectory = $storageDirectory ?? storage_path('app/plume/search');
    }

    /**
     * @return array{signature: string, records: list<array<string, mixed>>}
     */
    public function get(Vault $vault, ?string $language = null, ?string $version = null): array
    {
        $builder = new SearchIndexBuilder($vault, $language, $version);
        $signature = $builder->signature();
        $path = $this->cachePath($vault, $language, $version, $signature);

        $cached = $this->readCached($path);

        if ($cached !== null) {
            return ['signature' => $signature, 'records' => $cached];
        }

        $records = $builder->build();
        $this->writeCached($path, $records);
        $this->cleanStaleEntries($vault, $language, $version, $signature);

        return ['signature' => $signature, 'records' => $records];
    }

    /**
     * @return ?list<array<string, mixed>>
     */
    private function readCached(string $path): ?array
    {
        if (! is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $decoded = json_decode($contents, true);

        if (! is_array($decoded)) {
            return null;
        }

        /** @var list<array<string, mixed>> $decoded */
        return $decoded;
    }

    /**
     * @param  list<array<string, mixed>>  $records
     */
    private function writeCached(string $path, array $records): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $json = json_encode($records, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            return;
        }

        file_put_contents($path, $json);
    }

    private function cleanStaleEntries(Vault $vault, ?string $language, ?string $version, string $currentSignature): void
    {
        $pattern = $this->storageDirectory.'/'.$this->keyPrefix($vault, $language, $version).'*.json';

        foreach (glob($pattern) ?: [] as $file) {
            if (! str_ends_with($file, $currentSignature.'.json')) {
                @unlink($file);
            }
        }
    }

    private function cachePath(Vault $vault, ?string $language, ?string $version, string $signature): string
    {
        return $this->storageDirectory.'/'.$this->keyPrefix($vault, $language, $version).$signature.'.json';
    }

    private function keyPrefix(Vault $vault, ?string $language, ?string $version): string
    {
        return implode('--', [
            $this->sanitize(trim($vault->getPrefix(), '/')) ?: 'root',
            $this->sanitize($language ?? ''),
            $this->sanitize($version ?? ''),
            '',
        ]);
    }

    private function sanitize(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]+/', '_', $value) ?? '';
    }
}
