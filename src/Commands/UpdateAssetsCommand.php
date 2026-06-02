<?php

declare(strict_types=1);

namespace Meetplume\Plume\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class UpdateAssetsCommand extends Command
{
    protected $signature = 'plume:update-assets';

    protected $description = 'Refresh Plume\'s published assets, removing stale ones before republishing';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $target = public_path('vendor/plume');

        if ($this->files->isDirectory($target)) {
            $this->files->deleteDirectory($target);
            $this->components->info('Removed stale assets at public/vendor/plume.');
        }

        $this->call('vendor:publish', [
            '--tag' => 'plume-assets',
            '--force' => true,
        ]);

        $this->components->info('Plume assets are up to date.');

        return self::SUCCESS;
    }
}
