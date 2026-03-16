<?php

use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\ThemeConfig;
use Meetplume\Plume\Vault;

beforeEach(function (): void {
    $this->configDir = sys_get_temp_dir().'/plume_vault_test_'.uniqid();
    mkdir($this->configDir);
    $this->configPath = $this->configDir.'/config.yml';
    ThemeConfig::write($this->configPath, ['theme' => 'default', 'primary' => 'neutral', 'gray' => 'neutral', 'radius' => 'medium', 'spacing' => 'default', 'dark' => false]);

    $configDir = $this->configDir;
    $vault = new class($configDir) extends Vault
    {
        protected string $prefix = '/test-vault/docs';

        protected string $layout = 'docs';

        public function __construct(string $path)
        {
            $this->path = $path;
        }
    };

    Plume::configure()->vaults([]);
    $config = Plume::getConfiguration();
    $reflection = new ReflectionProperty($config, 'vaults');
    $reflection->setValue($config, ['test-vault/docs' => $vault]);
});

afterEach(function (): void {
    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }
    if (is_dir($this->configDir)) {
        rmdir($this->configDir);
    }
});

it('updates config file and returns resolved values', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'vault' => 'test-vault/docs',
        'primary' => 'blue',
        'radius' => 'large',
    ]);

    $response->assertSuccessful();
    $response->assertJsonFragment(['primary' => 'blue', 'radius' => 'large']);

    $config = new ThemeConfig($this->configPath);
    expect($config->toArray()['primary'])->toBe('blue');
    expect($config->toArray()['radius'])->toBe('large');
});

it('returns 422 for invalid radius', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'vault' => 'test-vault/docs',
        'radius' => 'invalid',
    ]);

    $response->assertUnprocessable();
});

it('returns 422 when vault is missing', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'primary' => 'blue',
    ]);

    $response->assertUnprocessable();
});

it('switches preset when theme sent alone', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'vault' => 'test-vault/docs',
        'theme' => 'ocean',
    ]);

    $response->assertSuccessful();

    $content = file_get_contents($this->configPath);
    expect($content)->toContain('theme: ocean');
    expect($content)->not->toContain('primary:');
});

it('resets to defaults', function (): void {
    $this->postJson('/_plume/customizer', ['vault' => 'test-vault/docs', 'primary' => 'red']);

    $response = $this->postJson('/_plume/customizer/reset', ['vault' => 'test-vault/docs']);

    $response->assertSuccessful();
    $response->assertJsonFragment(['primary' => 'neutral', 'radius' => 'medium']);
});

it('preserves theme key on reset', function (): void {
    $response = $this->postJson('/_plume/customizer/reset', ['vault' => 'test-vault/docs']);

    $response->assertSuccessful();

    $content = file_get_contents($this->configPath);
    expect($content)->toContain('theme: default');
});
