<?php

declare(strict_types=1);

use Meetplume\Plume\Actions\SearchIndexCache;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

$fixturePath = __DIR__.'/../fixtures/search';

function makeCacheVault(string $absolutePath, string $prefix = '/docs'): Vault
{
    return new class($absolutePath, $prefix) extends Vault
    {
        public function __construct(string $absolutePath, string $prefix)
        {
            $reflection = new ReflectionClass($this);

            foreach (['path' => $absolutePath, 'prefix' => $prefix, 'discovery' => Discovery::Auto] as $property => $value) {
                $prop = $reflection->getProperty($property);
                $prop->setValue($this, $value);
            }
        }
    };
}

beforeEach(function (): void {
    $this->cacheDir = sys_get_temp_dir().'/plume-search-cache-'.uniqid();
});

afterEach(function (): void {
    if (is_dir($this->cacheDir)) {
        foreach (glob($this->cacheDir.'/*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($this->cacheDir);
    }
});

it('returns records and a signature', function () use ($fixturePath): void {
    $vault = makeCacheVault($fixturePath);

    $result = new SearchIndexCache($this->cacheDir)->get($vault);

    expect($result)->toHaveKeys(['signature', 'records'])
        ->and($result['signature'])->toHaveLength(32)
        ->and($result['records'])->toHaveCount(3);
});

it('writes a JSON file on first call', function () use ($fixturePath): void {
    $vault = makeCacheVault($fixturePath);

    new SearchIndexCache($this->cacheDir)->get($vault);

    $files = glob($this->cacheDir.'/*.json') ?: [];

    expect($files)->toHaveCount(1);
});

it('reuses cached file on second call without rebuilding', function () use ($fixturePath): void {
    $vault = makeCacheVault($fixturePath);
    $cache = new SearchIndexCache($this->cacheDir);

    $first = $cache->get($vault);

    $files = glob($this->cacheDir.'/*.json') ?: [];
    $cacheFile = $files[0];
    $originalContents = file_get_contents($cacheFile);

    file_put_contents($cacheFile, json_encode([['id' => 'sentinel', 'slug' => 'sentinel']]));

    $second = $cache->get($vault);

    expect($second['signature'])->toBe($first['signature'])
        ->and($second['records'])->toBe([['id' => 'sentinel', 'slug' => 'sentinel']]);

    file_put_contents($cacheFile, (string) $originalContents);
});

it('invalidates and rebuilds when a source file mtime changes', function () use ($fixturePath): void {
    $vault = makeCacheVault($fixturePath);
    $cache = new SearchIndexCache($this->cacheDir);

    $first = $cache->get($vault);

    $file = $fixturePath.'/guides/deploy.md';
    $originalMtime = filemtime($file);
    touch($file, $originalMtime + 5);

    $second = $cache->get($vault);

    touch($file, $originalMtime);

    expect($second['signature'])->not->toBe($first['signature']);
});

it('removes stale cache files for the same vault/lang/version on rebuild', function () use ($fixturePath): void {
    $vault = makeCacheVault($fixturePath);
    $cache = new SearchIndexCache($this->cacheDir);

    $cache->get($vault);

    $file = $fixturePath.'/guides/deploy.md';
    $originalMtime = filemtime($file);
    touch($file, $originalMtime + 5);

    $cache->get($vault);

    touch($file, $originalMtime);

    $files = glob($this->cacheDir.'/*.json') ?: [];

    expect($files)->toHaveCount(1);
});

it('keeps cache entries for different vaults independent', function () use ($fixturePath): void {
    $vaultA = makeCacheVault($fixturePath, '/docs');
    $vaultB = makeCacheVault($fixturePath, '/blog');
    $cache = new SearchIndexCache($this->cacheDir);

    $cache->get($vaultA);
    $cache->get($vaultB);

    $files = glob($this->cacheDir.'/*.json') ?: [];

    expect($files)->toHaveCount(2);
});
