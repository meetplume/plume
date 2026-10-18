<?php

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Plume;
use Meetplume\Plume\ThemeConfig;
use Meetplume\Plume\Vault;

class CustomizerTestVault extends Vault
{
    public static string $contentPath = '';

    public static bool $allowed = true;

    protected string $prefix = '/test-vault/docs';

    protected string $layout = 'docs';

    protected Discovery $discovery = Discovery::Auto;

    public function getPath(): string
    {
        return self::$contentPath;
    }

    public function canAccess(Request $request): bool
    {
        return self::$allowed;
    }
}

beforeEach(function (): void {
    $this->configDir = sys_get_temp_dir().'/plume_vault_test_'.uniqid();
    mkdir($this->configDir);
    file_put_contents($this->configDir.'/index.md', "# Home\n");
    $this->configPath = $this->configDir.'/config.yml';
    ThemeConfig::write($this->configPath, ['theme' => 'default', 'primary' => 'neutral', 'gray' => 'neutral', 'radius' => 'medium', 'spacing' => 'default', 'dark' => false]);

    CustomizerTestVault::$contentPath = $this->configDir;
    CustomizerTestVault::$allowed = true;

    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([CustomizerTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
});

afterEach(function (): void {
    foreach (glob($this->configDir.'/*') ?: [] as $file) {
        unlink($file);
    }
    if (is_dir($this->configDir)) {
        rmdir($this->configDir);
    }
});

it('updates config file and returns resolved values', function (): void {
    $response = $this->postJson('/test-vault/docs/_plume/customizer', [
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
    $response = $this->postJson('/test-vault/docs/_plume/customizer', [
        'radius' => 'invalid',
    ]);

    $response->assertUnprocessable();
});

it('refuses a reader the vault turns away', function (): void {
    CustomizerTestVault::$allowed = false;

    $this->postJson('/test-vault/docs/_plume/customizer', ['primary' => 'blue'])->assertForbidden();

    expect(file_get_contents($this->configPath))->not->toContain('primary: blue');
});

it('switches preset when theme sent alone', function (): void {
    $response = $this->postJson('/test-vault/docs/_plume/customizer', [
        'theme' => 'ocean',
    ]);

    $response->assertSuccessful();

    $content = file_get_contents($this->configPath);
    expect($content)->toContain('theme: ocean');
    expect($content)->not->toContain('primary:');
});

it('resets to defaults', function (): void {
    $this->postJson('/test-vault/docs/_plume/customizer', ['primary' => 'red']);

    $response = $this->postJson('/test-vault/docs/_plume/customizer/reset');

    $response->assertSuccessful();
    $response->assertJsonFragment(['primary' => 'neutral', 'radius' => 'medium']);
});

it('preserves theme key on reset', function (): void {
    $response = $this->postJson('/test-vault/docs/_plume/customizer/reset');

    $response->assertSuccessful();

    $content = file_get_contents($this->configPath);
    expect($content)->toContain('theme: default');
});
