<?php

namespace App\Models;

use SplFileInfo;
use App\Support\AvailableLanguages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Sushi\Sushi;

class ContentFile extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return collect(File::allFiles(config('content.path', base_path('content'))))
            ->map(fn (SplFileInfo $file) => [
                'relativePath' => static::getContentPath($file->getPath()),
                'relativePathname' => static::getContentPath($file->getPathname()),
                'filename' => $file->getFilename(),
                'extension' => $file->getExtension(),
                'realPath' => $file->getRealPath(),
            ])
            ->toArray();
    }

    public static function getContentPath(string $path): string
    {
        return str($path)
            ->after(config('content.path', base_path('content')))
            ->replaceStart('/', '')
            ->value();
    }

    protected function sushiShouldCache(): bool
    {
        return app()->isProduction();
    }

    protected function isLanguage(string $subdirectory): bool
    {
        return array_key_exists($subdirectory, AvailableLanguages::all());
    }

    protected function isVersion(string $subdirectory): bool
    {
        return (bool) preg_match('/^v?\d+(\.\d+)*$/', $subdirectory);
    }

    protected function normalizeSlug(string $filename): string
    {
        return $filename === 'index' ? '' : $filename;
    }

    protected function parseFilePath(string $relativePathname): array
    {
        $parts = explode('/', trim($relativePathname, '/'));
        $lastPart = array_pop($parts);
        $filename = Str::replaceLast('.md', '', $lastPart);

        $result = [
            'contentType' => null,
            'version' => null,
            'language' => null,
            'slug' => null,
        ];

        if (count($parts) === 0) {
            $result['slug'] = $this->normalizeSlug($filename);
            return $result;
        }

        $result['contentType'] = array_shift($parts);

        if (count($parts) === 0) {
            $result['slug'] = $this->normalizeSlug($filename);
            return $result;
        }

        $firstSubdir = $parts[0];

        if ($this->isVersion($firstSubdir)) {
            $result['version'] = array_shift($parts);

            if (count($parts) === 0) {
                $result['slug'] = $this->normalizeSlug($filename);
                return $result;
            }

            if ($this->isLanguage($parts[0])) {
                $result['language'] = array_shift($parts);
            }
        } elseif ($this->isLanguage($firstSubdir)) {
            $result['language'] = array_shift($parts);
        }

        // Build the slug from remaining parts
        if (count($parts) > 0) {
            $parts[] = $filename;
            $result['slug'] = implode('/', $parts);
        } else {
            $result['slug'] = $this->normalizeSlug($filename);
        }

        return $result;
    }

    public function isPath(string $path, string $locale): bool
    {
        $fileParsed = $this->parseFilePath($this->relativePathname);
        $requestPath = trim($path, '/');
        $requestParts = $requestPath !== '' ? explode('/', $requestPath) : [];

        if (count($requestParts) === 0) {
            return $fileParsed['slug'] === '' && $fileParsed['language'] === $locale;
        }

        $requestContentType = array_shift($requestParts);

        if ($fileParsed['contentType'] !== $requestContentType) {
            return false;
        }

        $requestVersion = null;
        if (count($requestParts) > 0 && $this->isVersion($requestParts[0])) {
            $requestVersion = array_shift($requestParts);
        }

        if ($fileParsed['version'] !== $requestVersion) {
            return false;
        }

        if ($fileParsed['language'] !== $locale) {
            return false;
        }

        $requestSlug = count($requestParts) > 0 ? implode('/', $requestParts) : '';

        return $fileParsed['slug'] === $requestSlug;
    }
}
