<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Sushi\Sushi;

class ContentFile extends Model
{
    use Sushi;

    public function getRows()
    {
        return collect(File::allFiles(base_path('content')))
            ->map(function (\SplFileInfo $file) {

                $data = [
                    'relativePath' => static::getContentPath($file->getPath()),
                    'relativePathname' => static::getContentPath($file->getPathname()),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'realPath' => $file->getRealPath(),
                ];

                return $data;
            })
            ->toArray();
    }

    public static function getContentPath(string $path): string
    {
        return str($path)
            ->after(base_path('content'))
            ->replaceStart('/', '')
            ->value();
    }

    protected function sushiShouldCache(): bool
    {
        return app()->isProduction();
    }

    public function isPath(string $path, string $locale): bool
    {
        $localizedPath = str($path)->replaceStart('/', '');

        if($localizedPath->contains('/')) {
            $localizedPath = $localizedPath->replaceFirst('/', "/{$locale}/");
        } else {
            $localizedPath = $localizedPath->append("/{$locale}");
        }

        $instanceLocalizedPath = str($this->relativePathname);

        if($instanceLocalizedPath->endsWith('index.md')) {
            $instanceLocalizedPath = $instanceLocalizedPath->chopEnd("/index.md");
        } else {
            $instanceLocalizedPath = $instanceLocalizedPath->chopEnd('.md');
        }

        return $localizedPath->chopEnd('/')->value() === $instanceLocalizedPath->chopEnd('/')->value();
    }
}
