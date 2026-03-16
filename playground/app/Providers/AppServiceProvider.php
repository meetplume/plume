<?php

namespace App\Providers;

use App\Plume\LaravelZeroDocsVault;
use App\Plume\MingleJsDocsVault;
use App\Plume\PlumeDocsVault;
use Illuminate\Support\ServiceProvider;
use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\Footer;
use Meetplume\Plume\Header;
use Meetplume\Plume\Social;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Plume::configure()
            ->name('Plume Playground')
            ->theme('default')
            ->header(
                Header::make('header1')
                    ->socials([
                        Social::github('https://github.com/meetplume/plume'),
                    ])
            )
            ->footer(
                Footer::make('footer1')
                    ->text('Built with Plume')
            )
            ->vaults([
                PlumeDocsVault::class,
                LaravelZeroDocsVault::class,
                MingleJsDocsVault::class,
            ]);
    }
}
