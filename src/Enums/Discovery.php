<?php

declare(strict_types=1);

namespace Meetplume\Plume\Enums;

enum Discovery
{
    /**
     * Pages from navigation(), tabs(), versions(), and pages().
     * No filesystem scanning. Everything declared in PHP.
     */
    case Manual;

    /**
     * Routes discovered from .md files on disk (recursively).
     * navigation() still used for sidebar UI.
     * pages() for overrides.
     */
    case Mapped;

    /**
     * Routes and navigation derived from the filesystem.
     * Directory names become NavGroups, .md files become Pages.
     * Frontmatter controls title, slug, order, icon.
     */
    case Auto;
}
