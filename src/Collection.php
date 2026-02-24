<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Enums\CodeTheme;
use Meetplume\Plume\Http\Controllers\PageController;

final class Collection
{
    private ?string $title = null;

    private ?string $description = null;

    private ?CodeTheme $codeThemeLight = null;

    private ?CodeTheme $codeThemeDark = null;

    /** @var array<int, NavGroup|PageItem> */
    private array $navigation = [];

    /** @var array<string, PageItem> */
    private array $resolvedPages = [];

    public function __construct(
        public readonly string $prefix,
        public readonly string $contentPath,
    ) {}

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function codeTheme(CodeTheme $light, CodeTheme $dark): self
    {
        $this->codeThemeLight = $light;
        $this->codeThemeDark = $dark;

        return $this;
    }

    /**
     * @param  array<int, NavGroup|PageItem>  $navigation
     */
    public function navigation(array $navigation): self
    {
        $this->navigation = $navigation;
        $this->resolvePages();
        $this->registerRoutes();

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCodeThemeLight(): ?CodeTheme
    {
        return $this->codeThemeLight;
    }

    public function getCodeThemeDark(): ?CodeTheme
    {
        return $this->codeThemeDark;
    }

    /**
     * @return array<int, NavGroup|PageItem>
     */
    public function getNavigation(): array
    {
        return $this->navigation;
    }

    public function resolveFilePath(PageItem $page): string
    {
        return rtrim($this->contentPath, '/').'/'.$page->getPath();
    }

    /**
     * Resolve the label for a page, reading frontmatter if no explicit label is set.
     */
    public function resolvePageLabel(PageItem $page): string
    {
        if ($page->getLabel() !== null) {
            return $page->getLabel();
        }

        $filePath = $this->resolveFilePath($page);

        if (file_exists($filePath)) {
            $frontmatter = Frontmatter::parse((string) file_get_contents($filePath));

            if (isset($frontmatter['title']) && is_string($frontmatter['title'])) {
                return $frontmatter['title'];
            }
        }

        return str($page->key)->replace('-', ' ')->title()->toString();
    }

    /**
     * Build the navigation structure as an array for Inertia props.
     *
     * @return list<array{
     *     type: 'group',
     *     key: string,
     *     label: string,
     *     icon: string|null,
     *     pages: list<array{
     *       type: string,
     *       key: string,
     *       label: string,
     *       slug: string,
     *       href: string,
     *       hidden: bool,
     *       active: bool
     *     }>}|array{
     *     type: string,
     *     key: string,
     *     label: string,
     *     slug: string,
     *     href: string,
     *     hidden: bool,
     *     active: bool
     *   }>
     */
    public function toNavigationArray(?string $currentSlug = null): array
    {
        $items = [];

        foreach ($this->navigation as $item) {
            if ($item instanceof NavGroup) {
                $pages = [];

                foreach ($item->getPages() as $page) {
                    $pages[] = $this->pageToArray($page, $currentSlug);
                }

                $items[] = [
                    'type' => 'group',
                    'key' => $item->key,
                    'label' => $item->getLabel(),
                    'icon' => $item->getIcon(),
                    'pages' => $pages,
                ];
            } elseif ($item instanceof PageItem) {
                $items[] = $this->pageToArray($item, $currentSlug);
            }
        }

        return $items;
    }

    /**
     * @return array{type: string, key: string, label: string, slug: string, href: string, hidden: bool, active: bool}
     */
    private function pageToArray(PageItem $page, ?string $currentSlug = null): array
    {
        $slug = $page->getSlug();

        return [
            'type' => 'page',
            'key' => $page->key,
            'label' => $this->resolvePageLabel($page),
            'slug' => $slug,
            'href' => '/'.trim($this->prefix, '/').'/'.$slug,
            'hidden' => $page->isHidden(),
            'active' => $currentSlug === $slug,
        ];
    }

    private function resolvePages(): void
    {
        foreach ($this->navigation as $item) {
            if ($item instanceof NavGroup) {
                foreach ($item->getPages() as $page) {
                    $this->resolvedPages[$page->getSlug()] = $page;
                }
            } elseif ($item instanceof PageItem) {
                $this->resolvedPages[$item->getSlug()] = $item;
            }
        }
    }

    private function registerRoutes(): void
    {
        $prefix = trim($this->prefix, '/');

        foreach ($this->resolvedPages as $slug => $page) {
            Route::get(sprintf('%s/%s', $prefix, $slug), PageController::class)
                ->defaults('collection', $this)
                ->defaults('pageItem', $page)
                ->name(sprintf('plume.%s.%s', $prefix, $slug));
        }
    }
}
