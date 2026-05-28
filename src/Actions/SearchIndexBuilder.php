<?php

declare(strict_types=1);

namespace Meetplume\Plume\Actions;

use Meetplume\Plume\Frontmatter;
use Meetplume\Plume\Page;
use Meetplume\Plume\Tab;
use Meetplume\Plume\Vault;

final readonly class SearchIndexBuilder
{
    public function __construct(
        private Vault $vault,
        private ?string $language = null,
        private ?string $version = null,
    ) {}

    /**
     * @return list<array{
     *     id: string,
     *     slug: string,
     *     title: string,
     *     description: string,
     *     headings: list<string>,
     *     body: string,
     *     href: string,
     *     group: ?string,
     * }>
     */
    public function build(): array
    {
        $records = [];

        foreach ($this->collectEntries() as $entry) {
            $record = $this->buildRecord($entry['page'], $entry['group'], $entry['tab']);

            if ($record !== null) {
                $records[] = $record;
            }
        }

        return $records;
    }

    /**
     * Cheap fingerprint of all source files contributing to this index.
     *
     * Hashes (resolved path → mtime) without reading any file contents,
     * so cache layers can decide whether to rebuild without parsing markdown.
     */
    public function signature(): string
    {
        $parts = [];

        foreach ($this->collectEntries() as $entry) {
            $path = $this->vault->resolveFilePath($entry['page'], $this->language, $this->version);

            if (! file_exists($path)) {
                continue;
            }

            $parts[] = $path.':'.filemtime($path);
        }

        sort($parts);

        return md5(implode("\n", $parts));
    }

    /**
     * @return list<array{page: Page, group: ?string, tab: ?string}>
     */
    private function collectEntries(): array
    {
        $entries = [];
        $seen = [];

        foreach ($this->resolveTabKeys() as $tabKey) {
            foreach ($this->vault->resolveNavigation($this->language, $this->version, $tabKey) as $group) {
                foreach ($group->getPages() as $page) {
                    if ($page->isHidden()) {
                        continue;
                    }

                    $slug = $page->getSlug();

                    if (isset($seen[$slug])) {
                        continue;
                    }

                    $seen[$slug] = true;
                    $entries[] = ['page' => $page, 'group' => $group->getLabel(), 'tab' => $tabKey];
                }
            }
        }

        foreach ($this->vault->pages() as $page) {
            if ($page->isHidden()) {
                continue;
            }

            $slug = $page->getSlug();

            if (isset($seen[$slug])) {
                continue;
            }

            $seen[$slug] = true;
            $entries[] = ['page' => $page, 'group' => null, 'tab' => null];
        }

        return $entries;
    }

    /**
     * @return list<?string>
     */
    private function resolveTabKeys(): array
    {
        $tabs = $this->activeTabs();

        if ($tabs === []) {
            return [null];
        }

        return array_values(array_map(fn (Tab $tab): string => $tab->key, $tabs));
    }

    /**
     * @return list<Tab>
     */
    private function activeTabs(): array
    {
        if ($this->version !== null) {
            foreach ($this->vault->versions() as $versionObj) {
                if ($versionObj->key === $this->version && $versionObj->hasTabs()) {
                    return array_values($versionObj->getTabs() ?? []);
                }
            }
        }

        return array_values($this->vault->tabs());
    }

    /**
     * @return ?array{
     *     id: string,
     *     slug: string,
     *     title: string,
     *     description: string,
     *     headings: list<string>,
     *     body: string,
     *     href: string,
     *     group: ?string,
     * }
     */
    private function buildRecord(Page $page, ?string $group, ?string $tab): ?array
    {
        $filePath = $this->vault->resolveFilePath($page, $this->language, $this->version);

        if (! file_exists($filePath)) {
            return null;
        }

        $raw = (string) file_get_contents($filePath);
        $frontmatter = Frontmatter::parse($raw);
        $body = $this->stripFrontmatter($raw);

        return [
            'id' => $page->getSlug(),
            'slug' => $page->getSlug(),
            'title' => $this->extractTitle($frontmatter, $page, $filePath, $body),
            'description' => $this->extractDescription($frontmatter),
            'headings' => $this->extractHeadings($body),
            'body' => $this->stripMarkdown($body),
            'href' => $this->buildHref($page->getSlug(), $tab),
            'group' => $group,
        ];
    }

    /**
     * @param  array<string, mixed>  $frontmatter
     */
    private function extractTitle(array $frontmatter, Page $page, string $filePath, string $body): string
    {
        if (isset($frontmatter['title']) && is_string($frontmatter['title'])) {
            return $frontmatter['title'];
        }

        if (preg_match('/^#\s+(.+?)\s*#*\s*$/m', $body, $matches) === 1) {
            return trim($matches[1]);
        }

        return $page->resolveLabel($filePath);
    }

    /**
     * @param  array<string, mixed>  $frontmatter
     */
    private function extractDescription(array $frontmatter): string
    {
        if (isset($frontmatter['description']) && is_string($frontmatter['description'])) {
            return $frontmatter['description'];
        }

        return '';
    }

    /**
     * @return list<string>
     */
    private function extractHeadings(string $body): array
    {
        $clean = preg_replace('/```.*?```/s', '', $body) ?? $body;

        preg_match_all('/^(#{1,3})\s+(.+?)\s*#*\s*$/m', $clean, $matches);

        return array_values(array_map(trim(...), $matches[2] ?? []));
    }

    private function stripFrontmatter(string $raw): string
    {
        return preg_replace('/^---\r?\n.*?\r?\n---\r?\n?/s', '', $raw, 1) ?? $raw;
    }

    private function stripMarkdown(string $body): string
    {
        $text = $body;

        $text = preg_replace('/```[a-zA-Z0-9_-]*\r?\n.*?\r?\n```/s', ' ', $text) ?? $text;
        $text = preg_replace('/`[^`\n]+`/', ' ', $text) ?? $text;
        $text = preg_replace('/!\[[^\]]*\]\([^)]*\)/', ' ', $text) ?? $text;
        $text = preg_replace('/\[([^\]]+)\]\([^)]*\)/', '$1', $text) ?? $text;
        $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;
        $text = preg_replace('/(\*{1,3}|_{1,3}|~{2})(.+?)\1/', '$2', $text) ?? $text;
        $text = preg_replace('/^>\s?/m', '', $text) ?? $text;
        $text = preg_replace('/^\s*[-*+]\s+/m', '', $text) ?? $text;
        $text = preg_replace('/^\s*\d+\.\s+/m', '', $text) ?? $text;
        $text = preg_replace('/<[^>]+>/', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function buildHref(string $slug, ?string $tab): string
    {
        $segments = [trim($this->vault->getPrefix(), '/')];

        if ($this->language !== null) {
            $segments[] = $this->language;
        }

        if ($this->version !== null) {
            $segments[] = $this->version;
        }

        if ($tab !== null) {
            $segments[] = $tab;
        }

        if ($slug !== '/') {
            $segments[] = $slug;
        }

        return '/'.implode('/', array_filter($segments, fn (string $segment): bool => $segment !== ''));
    }
}
