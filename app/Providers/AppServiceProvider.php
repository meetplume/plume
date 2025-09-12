<?php

/** @noinspection NonAsciiCharacters */

namespace App\Providers;

use Pan\PanConfiguration;
use App\Enums\SiteSettings;
use Illuminate\Support\Carbon;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\DB;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Support\AvailableLanguages;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Mixins\RichContentRendererMixin;
use Illuminate\Validation\Rules\Password;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentAsset;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use CharlieEtienne\PaletteGenerator\PaletteGenerator;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Define the application's singletons.
     *
     * @var array<class-string, class-string>
     */
    public $singletons = [
        \Filament\Auth\Http\Responses\Contracts\LoginResponse::class => \App\Auth\Http\Responses\UserPanelLoginResponse::class,
        \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class => \App\Auth\Http\Responses\UserPanelLogoutResponse::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Theme::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->laravelGoodies🤘();
        $this->setAppLanguageDefaults();
        $this->setAppPasswordsDefaults();
        $this->setAppAnalyticsDefaults();
        $this->setFilamentDefaults();
        $this->registerFilamentAssets();
        $this->registerThemeBladeDirectives();
    }

    public function laravelGoodies🤘(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
        URL::forceScheme('https');
        Vite::useAggressivePrefetching();
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
    }

    public function setAppLanguageDefaults(): void
    {
        App::setLocale(SiteSettings::DEFAULT_LANGUAGE->get() ?? 'en');
        App::setFallbackLocale(SiteSettings::FALLBACK_LANGUAGE->get() ?? 'en');
        Carbon::setLocale(SiteSettings::DEFAULT_LANGUAGE->get() ?? 'en');
        LaravelLocalization::setSupportedLocales(AvailableLanguages::get());
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(SiteSettings::LANGUAGES->get());
        });
    }

    public function setAppPasswordsDefaults(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->uncompromised();
        });
    }

    public function setFilamentDefaults(): void
    {
        FilamentColor::register([
            'primary' => $this->cachedGeneratedPalette(SiteSettings::PRIMARY_COLOR->get()),
            'gray' => Color::{ucfirst(SiteSettings::NEUTRAL_COLOR->get())},
        ]);

        RichContentRenderer::mixin(new RichContentRendererMixin());
    }

    public function registerFilamentAssets(): void
    {
        FilamentAsset::register([
            Js::make('rich-content-plugins/IdExtension', __DIR__ . '/../../resources/js/dist/filament/rich-content-plugins/IdExtension.js')->loadedOnRequest(),
        ]);
    }

    public function setAppAnalyticsDefaults(): void
    {
        PanConfiguration::maxAnalytics(10000);
    }

    public function cachedGeneratedPalette(string $color): array
    {
        try {
            return cache()->rememberForever("primary_palette_generated", fn () => PaletteGenerator::generatePalette($color));
        } catch (\Exception $e) {
            // Bypass cache if the database is not available (e.g., during CI or tests)
            return PaletteGenerator::generatePalette($color);
        }
    }

    public function registerThemeBladeDirectives(): void
    {
        // @themeAsset directive - generates URL for theme assets
        Blade::directive('themeAsset', function ($expression) {
            return "<?php echo app(\App\Services\ThemeService::class)->getThemeAssetUrl($expression); ?>";
        });

        // @themePartial directive - includes theme-specific partials
        Blade::directive('themePartial', function ($expression) {
            return "<?php echo \$__env->make('themes.' . app(\App\Services\ThemeService::class)->getActiveTheme() . '.partials.' . $expression, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });

        // @themeConfig directive - gets theme configuration values
        Blade::directive('themeConfig', function ($expression) {
            return "<?php echo data_get(app(\App\Services\ThemeService::class)->getThemeConfig(), $expression); ?>";
        });

        // @hasThemePartial directive - checks if theme has a specific partial
        Blade::directive('hasThemePartial', function ($expression) {
            return "<?php if (app(\App\Services\ThemeService::class)->hasPartial($expression)): ?>";
        });

        Blade::directive('endHasThemePartial', function () {
            return '<?php endif; ?>';
        });

        // @themeStyle directive - includes theme's main stylesheet
        Blade::directive('themeStyle', function () {
            return "<?php if (file_exists(resource_path('themes/' . app(\App\Services\ThemeService::class)->getActiveTheme() . '/style.css'))): ?>
                        <link rel=\"stylesheet\" href=\"<?php echo app(\App\Services\ThemeService::class)->getThemeAssetUrl('style.css'); ?>\">
                    <?php endif; ?>";
        });
    }
}
