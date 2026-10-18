<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;

final class PlumeConfiguration
{
    /**
     * Plume renders its own Inertia pages, with their own asset version. The
     * host's Inertia middleware, when the `web` group carries one, would answer
     * every Plume visit with a 409 (version mismatch, hence a full reload) and
     * compute the host's shared props for nothing. Excluding the base class
     * also excludes the host's subclass (`HandleInertiaRequests`).
     */
    private const string HOST_INERTIA_MIDDLEWARE = 'Inertia\\Middleware';

    private ?string $name = null;

    private ?string $url = null;

    private ?string $logo = null;

    private ?string $logoDark = null;

    private ?string $favicon = null;

    private ?string $theme = null;

    private string $appearanceKey = 'theme';

    private ?Header $header = null;

    private ?Footer $footer = null;

    private bool $poweredBy = true;

    /** @var array<int, class-string<Vault>> */
    private array $vaultClasses = [];

    /** @var array<string, Vault> */
    private array $vaults = [];

    private bool $booted = false;

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function logo(string $light, ?string $dark = null): self
    {
        $this->logo = $light;
        $this->logoDark = $dark;

        return $this;
    }

    public function favicon(string $favicon): self
    {
        $this->favicon = $favicon;

        return $this;
    }

    public function theme(string $preset): self
    {
        $this->theme = $preset;

        return $this;
    }

    /**
     * The browser storage key (localStorage and cookie) that keeps the
     * light/dark choice. Set it to the host app's own key to share the choice
     * with the app; a stored `system` follows the operating system.
     */
    public function appearanceKey(string $key): self
    {
        $this->appearanceKey = $key;

        return $this;
    }

    public function header(Header $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function footer(Footer $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Whether the docs footer credits Plume ("Powered by Plume"). On by default.
     */
    public function poweredBy(bool $show = true): self
    {
        $this->poweredBy = $show;

        return $this;
    }

    /**
     * @param  array<int, class-string<Vault>>  $vaults
     */
    public function vaults(array $vaults): self
    {
        $this->vaultClasses = $vaults;

        return $this;
    }

    public function getName(): string
    {
        return $this->name ?? config('app.name', 'Laravel');
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function getLogoDark(): ?string
    {
        return $this->logoDark;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function showsPoweredBy(): bool
    {
        return $this->poweredBy;
    }

    public function getAppearanceKey(): string
    {
        return $this->appearanceKey;
    }

    public function getHeader(): ?Header
    {
        return $this->header;
    }

    public function getFooter(): ?Footer
    {
        return $this->footer;
    }

    /**
     * @return array<string, Vault>
     */
    public function getVaults(): array
    {
        return $this->vaults;
    }

    public function getVault(string $prefix): ?Vault
    {
        return $this->vaults[trim($prefix, '/')] ?? null;
    }

    /**
     * Boot the configuration: instantiate vaults, init theme, register routes.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        $this->initTheme();

        $router = new VaultRouter;

        foreach ($this->vaultClasses as $vaultClass) {
            /** @var Vault $vault */
            $vault = new $vaultClass;
            $prefix = trim($vault->getPrefix(), '/');
            $this->vaults[$prefix] = $vault;

            Route::middleware($vault->middleware())
                ->withoutMiddleware(self::HOST_INERTIA_MIDDLEWARE)
                ->group(fn () => $router->register($vault));
        }
    }

    /**
     * @return array{site: array{name: string, logo: ?string, logoDark: ?string, favicon: ?string, url: ?string}}
     */
    public function toSharedInertiaProps(): array
    {
        return [
            'site' => [
                'name' => $this->getName(),
                'logo' => $this->logo,
                'logoDark' => $this->logoDark,
                'favicon' => $this->favicon,
                'url' => $this->url,
            ],
        ];
    }

    private function initTheme(): void
    {
        if ($this->theme === null) {
            return;
        }

        $presetPath = __DIR__.'/../resources/presets/'.basename($this->theme).'.yml';

        if (file_exists($presetPath)) {
            $themeConfig = new ThemeConfig($presetPath);
            app()->instance(ThemeConfig::class, $themeConfig);
        }
    }
}
