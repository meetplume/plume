<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final readonly class FilesystemScanner
{
    public function __construct(
        private string $basePath,
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
                $fullPath = $this->basePath.'/'.$relativePath;
                $frontmatter = $this->readFrontmatter($fullPath);

                $groupMeta[$groupKey] = [];

                if (isset($frontmatter['group_icon']) && is_string($frontmatter['group_icon'])) {
                    $groupMeta[$groupKey]['icon'] = $frontmatter['group_icon'];
                }
            }
        }

        $navGroups = [];

        foreach ($grouped as $groupKey => $pages) {
            $sortedPages = $this->sortPages($pages);

            $group = NavGroup::make($groupKey)
                ->pages($sortedPages);

            if ($groupKey !== '_root') {
                $group->label(
                    str($groupKey)->replace('-', ' ')->title()->toString()
                );
            }

            $meta = $groupMeta[$groupKey] ?? [];

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
     * Find all .md files relative to basePath, excluding files starting with _.
     *
     * @return array<int, string>
     */
    private function findMarkdownFiles(): array
    {
        if (! is_dir($this->basePath)) {
            return [];
        }

        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->basePath,
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
                str_replace($this->basePath, '', $file->getPathname()),
                '/',
            );

            $files[] = $relativePath;
        }

        sort($files);

        return $files;
    }

    private function buildPage(string $relativePath): \Meetplume\Plume\Page
    {
        $fullPath = $this->basePath.'/'.$relativePath;
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
