<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final readonly class FilesystemScanner
{
    /**
     * @param  ?string  $fallbackBasePath  A folder whose files fill in the ones missing from `$basePath`
     *                                     (the default language, while a page is not translated yet).
     * @param  ?string  $fallbackLanguage  The language of that folder, set on the pages it fills in.
     */
    public function __construct(
        private string $basePath,
        private ?string $fallbackBasePath = null,
        private ?string $fallbackLanguage = null,
    ) {}

    /**
     * Collect all slugs from .md files on disk.
     *
     * @return array<int, string>
     */
    public function scanSlugs(): array
    {
        return array_map(
            fn (Page $page): string => $page->getSlug(),
            $this->scanPages(),
        );
    }

    /**
     * Scan for all .md files and return Page objects with frontmatter applied.
     *
     * @return array<int, Page>
     */
    public function scanPages(): array
    {
        $files = $this->findMarkdownFiles();
        $pages = [];

        foreach ($files as $relativePath) {
            $page = $this->buildPage($relativePath);

            $pages[] = $page;
        }

        return $this->sortPages($pages);
    }

    /**
     * Scan and build NavGroup structure from directory hierarchy.
     *
     * @return array<int, NavGroup>
     */
    public function scanNavigation(): array
    {
        $files = $this->findMarkdownFiles();

        /** @var array<string, array<int, Page>> */
        $grouped = [];

        /** @var array<string, array{label?: string, icon?: string, order?: int}> */
        $groupMeta = [];

        foreach ($files as $relativePath) {
            $page = $this->buildPage($relativePath);

            $directory = dirname($relativePath);
            $groupKey = $directory === '.' ? '_root' : $directory;

            $grouped[$groupKey] ??= [];
            $grouped[$groupKey][] = $page;

            if ($groupKey !== '_root' && ! isset($groupMeta[$groupKey])) {
                $frontmatter = $this->readFrontmatter($this->fullPath($relativePath));

                $groupMeta[$groupKey] = [];

                if (isset($frontmatter['group_icon']) && is_string($frontmatter['group_icon'])) {
                    $groupMeta[$groupKey]['icon'] = $frontmatter['group_icon'];
                }

                if (isset($frontmatter['group_label']) && is_string($frontmatter['group_label'])) {
                    $groupMeta[$groupKey]['label'] = $frontmatter['group_label'];
                }
            }
        }

        $navGroups = [];

        foreach ($grouped as $groupKey => $pages) {
            $sortedPages = $this->sortPages($pages);

            $group = NavGroup::make($groupKey)
                ->pages($sortedPages);

            $meta = $groupMeta[$groupKey] ?? [];

            $group->label(match ($groupKey) {
                '_root' => '',
                default => $meta['label'] ?? str($groupKey)->replace('-', ' ')->title()->toString(),
            });

            if (isset($meta['icon'])) {
                $group->icon($meta['icon']);
            }

            $navGroups[] = $group;
        }

        usort($navGroups, function (NavGroup $a, NavGroup $b): int {
            if ($a->key === '_root') {
                return -1;
            }

            if ($b->key === '_root') {
                return 1;
            }

            $aOrder = $this->getGroupOrder($a);
            $bOrder = $this->getGroupOrder($b);

            if ($aOrder !== $bOrder) {
                return $aOrder <=> $bOrder;
            }

            return $a->key <=> $b->key;
        });

        return $navGroups;
    }

    /**
     * Find all .md files relative to basePath, and those of the fallback folder
     * that basePath lacks, excluding files starting with _.
     *
     * @return array<int, string>
     */
    private function findMarkdownFiles(): array
    {
        $files = $this->listMarkdownFiles($this->basePath);

        if ($this->fallbackBasePath !== null) {
            $files = array_values(array_unique([...$files, ...$this->listMarkdownFiles($this->fallbackBasePath)]));
            sort($files);
        }

        return $files;
    }

    /**
     * The file behind a relative path: in basePath, or in the fallback folder when basePath lacks it.
     */
    private function fullPath(string $relativePath): string
    {
        if ($this->fallbackBasePath === null || is_file($this->basePath.'/'.$relativePath)) {
            return $this->basePath.'/'.$relativePath;
        }

        return $this->fallbackBasePath.'/'.$relativePath;
    }

    /**
     * @return array<int, string>
     */
    private function listMarkdownFiles(string $basePath): array
    {
        if (! is_dir($basePath)) {
            return [];
        }

        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $basePath,
                RecursiveDirectoryIterator::SKIP_DOTS,
            ),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $filename = $file->getFilename();

            if (str_starts_with($filename, '_')) {
                continue;
            }

            $relativePath = ltrim(
                str_replace($basePath, '', $file->getPathname()),
                '/',
            );

            $files[] = $relativePath;
        }

        sort($files);

        return $files;
    }

    private function buildPage(string $relativePath): Page
    {
        $fullPath = $this->fullPath($relativePath);
        $frontmatter = $this->readFrontmatter($fullPath);

        $filename = pathinfo($relativePath, PATHINFO_FILENAME);
        $directory = dirname($relativePath);

        $key = $directory === '.'
            ? $filename
            : $directory.'/'.$filename;

        $page = Page::make($key)->path($relativePath);

        if ($filename === 'index' && $directory === '.') {
            $page->slug('/');
        }

        if (isset($frontmatter['title']) && is_string($frontmatter['title'])) {
            $page->label($frontmatter['title']);
        }

        if (isset($frontmatter['slug']) && is_string($frontmatter['slug'])) {
            $page->slug($frontmatter['slug']);
        }

        if (isset($frontmatter['order']) && is_int($frontmatter['order'])) {
            $page->order($frontmatter['order']);
        }

        if (isset($frontmatter['hidden']) && $frontmatter['hidden'] === true) {
            $page->hidden();
        }

        if ($this->fallbackLanguage !== null && ! str_starts_with($fullPath, $this->basePath.'/')) {
            $page->language($this->fallbackLanguage);
        }

        return $page;
    }

    /**
     * @return array<string, mixed>
     */
    private function readFrontmatter(string $filePath): array
    {
        if (! file_exists($filePath)) {
            return [];
        }

        return Frontmatter::parse((string) file_get_contents($filePath));
    }

    /**
     * Sort pages by order (nulls last), then alphabetically by key.
     *
     * @param  array<int, Page>  $pages
     * @return array<int, Page>
     */
    private function sortPages(array $pages): array
    {
        usort($pages, function (Page $a, Page $b): int {
            $aOrder = $a->getOrder();
            $bOrder = $b->getOrder();

            if ($aOrder !== null && $bOrder !== null) {
                return $aOrder <=> $bOrder;
            }

            if ($aOrder !== null) {
                return -1;
            }

            if ($bOrder !== null) {
                return 1;
            }

            return $a->key <=> $b->key;
        });

        return $pages;
    }

    private function getGroupOrder(NavGroup $group): int
    {
        $pages = $group->getPages();

        if ($pages === []) {
            return PHP_INT_MAX;
        }

        $firstOrder = $pages[0]->getOrder();

        return $firstOrder ?? PHP_INT_MAX;
    }
}
