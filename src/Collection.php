<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Meetplume\Plume\Enums\Type;
use Symfony\Component\Yaml\Yaml;
use Meetplume\Plume\Enums\CodeTheme;
use Meetplume\Plume\Types\CollectionType;

class Collection
{
    private string $prefix;

    private string $contentPath;

    private Type $type = Type::Documentation;

    public CodeTheme $codeThemeLight = CodeTheme::GITHUB_LIGHT;

    public CodeTheme $codeThemeDark = CodeTheme::GITHUB_DARK;

    private CollectionType $typeDriver;

    private ?string $title = null;

    /**
     * @var array<int, array{slug: string, rawContent: string}>|null
     */
    private ?array $cachedFiles = null;

    public function __construct(string $prefix, string $contentPath)
    {
        $this->prefix = $prefix;
        $this->contentPath = rtrim($contentPath, '/');
        $this->typeDriver = $this->type->driver();
    }

    public function type(Type $type): self
    {
        $this->type = $type;
        $this->typeDriver = $type->driver();

        return $this;
    }

    public function codeTheme(CodeTheme $light, CodeTheme $dark): self
    {
        $this->codeThemeLight = $light;
        $this->codeThemeDark = $dark;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getContentPath(): string
    {
        return $this->contentPath;
    }

    public function getType(): CollectionType
    {
        return $this->typeDriver;
    }

    public function getCodeThemeLight(): CodeTheme
    {
        return $this->codeThemeLight;
    }

    public function getCodeThemeDark(): CodeTheme
    {
        return $this->codeThemeDark;
    }

    public function getTitle(): string
    {
        if ($this->title) {
            return $this->title;
        }

        $navigation = $this->readNavigation();
        if ($navigation && isset($navigation['title'])) {
            return $navigation['title'];
        }

        return ucfirst(trim($this->prefix, '/'));
    }

    /**
     * @return array<int, array{slug: string, title: string, description: string, rawContent: string}>
     */
    public function discoverFiles(): array
    {
        if ($this->cachedFiles !== null) {
            return $this->cachedFiles;
        }

        $files = glob($this->contentPath.'/*.md') ?: [];
        $pages = [];

        foreach ($files as $file) {
            $filename = basename($file, '.md');

            if (str_starts_with($filename, '_')) {
                continue;
            }

            $rawContent = file_get_contents($file);
            $frontmatter = self::parseFrontmatter($rawContent);

            $pages[] = [
                'slug' => $filename,
                'title' => $frontmatter['title'] ?? self::slugToTitle($filename),
                'description' => $frontmatter['description'] ?? '',
                'rawContent' => $rawContent,
            ];
        }

        usort($pages, fn (array $a, array $b): int => strcmp($a['slug'], $b['slug']));

        $this->cachedFiles = $pages;

        return $pages;
    }

    /**
     * @return array{slug: string, rawContent: string}|null
     */
    public function findFileBySlug(string $slug): ?array
    {
        $path = $this->contentPath.'/'.$slug.'.md';

        if (! file_exists($path)) {
            return null;
        }

        return [
            'slug' => $slug,
            'rawContent' => file_get_contents($path),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function readNavigation(): ?array
    {
        $yamlPath = $this->contentPath.'/_collection.yml';

        if (! file_exists($yamlPath)) {
            return null;
        }

        return Yaml::parseFile($yamlPath);
    }

    /**
     * @return array{prefix: string, title: string, type: string, codeThemeLight: string, codeThemeDark: string}
     */
    public function toArray(): array
    {
        return [
            'prefix' => $this->prefix,
            'title' => $this->getTitle(),
            'type' => $this->type->value,
            'codeThemeLight' => $this->codeThemeLight->value,
            'codeThemeDark' => $this->codeThemeDark->value,
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function parseFrontmatter(string $rawContent): array
    {
        if (! preg_match('/^---\r?\n(.*?)\r?\n---/s', $rawContent, $matches)) {
            return [];
        }

        $parsed = Yaml::parse($matches[1]);

        return is_array($parsed) ? $parsed : [];
    }

    private static function slugToTitle(string $slug): string
    {
        return ucwords(str_replace('-', ' ', $slug));
    }
}
