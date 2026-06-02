---
title: Contributing to Plume
description: "Internal documentation for people developing the Plume package — architecture, dev workflow, and the playground."
sections:
    -   type: hero2
        title: Develop |Plume|
        tagline: "Everything you need to work on the Plume package itself — architecture, the dev workflow, and the playground app you test against."
        command: composer setup && composer dev
        links:
            -   label: Get Started
                href: /docs-contributing/getting-started/introduction

    -   type: features
        columns: { default: 1, md: 3 }
        features:
            -   title: PHP-first architecture
                description: Vault classes are the unit of organization, Filament-style. Read the backend & frontend maps.
                icon: Layers
            -   title: Real playground app
                description: A full Laravel app consumes the package via symlink so you see changes live.
                icon: AppWindowMac
            -   title: Tested & typed
                description: Pest 4 on Testbench, 100% type coverage, Pint and Rector enforced in CI.
                icon: Terminal
---

# Contributing to Plume

This is the **internal documentation for developing the Plume package** — not the user-facing guide. If you want to *use* Plume in your own app, see the [Plume docs](/docs).

Plume is a Laravel package (`meetplume/plume`, namespace `Meetplume\Plume\`) that serves Markdown content as pages, docs, wikis and more. It is **PHP-first and Filament-inspired**: each `Vault` is a class, and overriding methods unlocks layers (languages → versions → tabs → navigation → pages).

## Where to start

1. [Overview](/docs-contributing/overview) — map of the codebase.
2. [Introduction](/docs-contributing/getting-started/introduction) — what the project is and how it's split.
3. [Development workflow](/docs-contributing/getting-started/development-workflow) — `composer setup`, `composer dev`, tests, scaffolding.
4. [Playground](/docs-contributing/getting-started/playground) — the app you develop against.
5. [PHP Backend](/docs-contributing/architecture/php-backend) and [Frontend Pipeline](/docs-contributing/architecture/frontend) — how it all works.
</content>
</invoke>
