<?php

declare(strict_types=1);

namespace Meetplume\Plume\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeVaultCommand extends Command
{
    protected $signature = 'plume:vault {name : The name of the vault (e.g. Blog, Docs)}';

    protected $description = 'Scaffold a starter Plume vault with a content folder and an index page';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $studly = Str::studly($this->argument('name'));
        $class = $studly.'Vault';
        $slug = Str::kebab($studly);
        $title = Str::headline($studly);

        $classPath = app_path('Plume/'.$class.'.php');
        $contentDir = 'content/'.$slug;
        $contentPath = base_path($contentDir.'/index.md');

        if ($this->files->exists($classPath)) {
            $this->components->error(sprintf('Vault [%s] already exists.', $class));

            return self::FAILURE;
        }

        $this->writeFile($classPath, $this->render('vault.stub', [
            'class' => $class,
            'prefix' => '/'.$slug,
            'path' => $contentDir,
        ]));

        if (! $this->files->exists($contentPath)) {
            $this->writeFile($contentPath, $this->render('vault-content.stub', [
                'title' => $title,
                'path' => $contentDir,
            ]));
        }

        $this->components->info(sprintf('Vault [%s] scaffolded successfully.', $class));
        $this->components->bulletList([
            'app/Plume/'.$class.'.php',
            $contentDir.'/index.md',
        ]);

        $this->newLine();
        $this->components->info('Register it in your AppServiceProvider::boot() method:');
        $this->line(sprintf(<<<EOT
        // app/Providers/AppServiceProvider.php

        use App\Plume\DocsGuideVault;
        use Meetplume\Plume\Facades\Plume;

        Plume::configure()
            ->vaults([
                DocsGuideVault::class,
            ]);

        EOT, $class));

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function render(string $stub, array $replacements): string
    {
        $contents = $this->files->get(__DIR__.'/../../resources/stubs/'.$stub);

        foreach ($replacements as $key => $value) {
            $contents = str_replace('{{ '.$key.' }}', $value, $contents);
        }

        return $contents;
    }

    private function writeFile(string $path, string $contents): void
    {
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $contents);
    }
}
