<?php

declare(strict_types=1);

namespace Meetplume\Plume\Actions;

use Meetplume\Plume\Frontmatter;
use Meetplume\Plume\Page;
use Meetplume\Plume\Tab;
use Meetplume\Plume\Vault;

final readonly class SearchIndexBuilder
{
    /**
     * Version of what build() outputs. Bump it whenever a change to Plume
     * alters the records for unchanged docs, so caches and ETags built by
     * an earlier release are dropped after an update.
     */
    public const int INDEX_FORMAT = 2;

    public function __construct(
        private Vault $vault,
        private ?string $language = null,
        private ?string $version = null,
        private int $indexFormat = self::INDEX_FORMAT,
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
     * The index format is mixed in, so a Plume update that changes the
     * records also changes the fingerprint.
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

        // Records of a vault that filters its pages carry their key and frontmatter.
        if ($this->vault->hasPageVisibility()) {
            $parts[] = 'visibility';
        }

        return md5($this->indexFormat."\n".implode("\n", $parts));
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
                    $entries[] = ['page' => $page, 'group' => $group->getLabel() !== '' ? $group->getLabel() : null, 'tab' => $tabKey];
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
            ...($this->vault->hasPageVisibility() ? ['key' => $page->key, 'frontmatter' => $frontmatter] : []),
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
        preg_match_all('/^(#{1,3})\s+(.+?)\s*#*\s*$/m', $this->stripCodeFences($body), $matches);

        return array_values(array_map(trim(...), $matches[2] ?? []));
    }

    private function stripFrontmatter(string $raw): string
    {
        return preg_replace('/^---\r?\n.*?\r?\n---\r?\n?/s', '', $raw, 1) ?? $raw;
    }

    /**
     * Drops fenced code blocks line by line, the way the renderer reads them:
     * backtick or tilde fences, closed only by the same character at least
     * as long, so a ```` block can show a ``` one.
     */
    private function stripCodeFences(string $markdown): string
    {
        $kept = [];
        $fence = null;

        foreach (preg_split('/\r?\n/', $markdown) ?: [] as $line) {
            if ($fence !== null) {
                if (preg_match('/^\s*'.$fence[0].'{'.strlen($fence).',}\s*$/', $line) === 1) {
                    $fence = null;
                }

                continue;
            }

            if (preg_match('/^\s*(`{3,}(?=[^`]*$)|~{3,})/', $line, $matches) === 1) {
                $fence = $matches[1];

                continue;
            }

            $kept[] = $line;
        }

        return implode("\n", $kept);
    }

    private function stripMarkdown(string $body): string
    {
        $text = $this->stripCodeFences($body);

        // Callouts: `:::tip[Title]` and `::: tip Title` keep their title, `:::` and `> [!TIP]` markers go.
        $text = preg_replace('/^[ \t]*:{3,}[ \t]*(?:[a-z][\w-]*)?[ \t]*(?:\[(.*)\])?[ \t]*(?:\{.*\})?[ \t]*(.*)$/im', '$1 $2', $text) ?? $text;
        $text = preg_replace('/^([ \t]*(?:>[ \t]*)+)\[![a-z]+\]/im', '$1', $text) ?? $text;

        // Inline code keeps its text, set aside so the rules below leave `array<Page>` or `snake_case_name` alone.
        $codeSpans = [];
        $text = preg_replace_callback('/(?<!`)(`+)(?!`)(.+?)(?<!`)\1(?!`)/', function (array $matches) use (&$codeSpans): string {
            $placeholder = "\x1A".count($codeSpans)."\x1A";
            $codeSpans[$placeholder] = trim($matches[2]);

            return $placeholder;
        }, $text) ?? $text;

        // Tables: the delimiter row goes, the cells keep their text.
        $text = preg_replace('/^(?=[^\n]*\|)(?=[^\n]*-)[ \t|:-]+$/m', '', $text) ?? $text;
        $text = str_replace('|', ' ', $text);

        $text = preg_replace('/!\[[^\]]*\]\([^)]*\)/', ' ', $text) ?? $text;
        $text = preg_replace('/\[([^\]]+)\]\([^)]*\)/', '$1', $text) ?? $text;
        $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;
        $text = preg_replace('/(\*{1,3}|_{1,3}|~{2})(.+?)\1/', '$2', $text) ?? $text;
        $text = preg_replace('/^>\s?/m', '', $text) ?? $text;
        $text = preg_replace('/^\s*[-*+]\s+/m', '', $text) ?? $text;
        $text = preg_replace('/^\s*\d+\.\s+/m', '', $text) ?? $text;
        $text = preg_replace('/<[^>]+>/', ' ', $text) ?? $text;
        $text = strtr($text, $codeSpans);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function buildHref(string $slug, ?string $tab): string
    {
        $segments = [trim($this->vault->getPrefix(), '/')];

        if ($this->language !== null && ! $this->vault->hasAppLanguage()) {
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
