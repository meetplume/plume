---
title: Content
description: Learn how to manage your content in Plume
---
## Database driven content

Documentation coming soon...
## Files based content

Your Plume app contains a `content` directory, which can be used to publish your content, written in Markdown.
#### Posts

Your [posts](#) should be placed into a `posts` subfolder, like so:
```
└── content/
    └── posts/
        ├── how-to-write-proper-markdown.md
        ├── obsidian-for-developers.md
        └── adding-custom-livewire-in-filament.md
```
#### Pages

Your [pages](#) will go into the `pages` subfolder:
```
└── content/
    └── pages/
        ├── about-me.md
        ├── legal.md
        └── privacy-policy.md
```
#### Docs

In the same fashion, your [documentation](#) should be in `docs` subdirectory:
```
└── content/
    └── docs/
        ├── getting-started.md
        └── index.md
```

#### Releases notes

Plume offers a beautiful [changelog](#) page out of the box. Releases notes should be located in the `releases` directory:
```
└── content/
    └── releases/
        ├── v2.0.md
        ├── v1.3.md
        ├── v1.2.md
        ├── v1.1.md
        └── v1.0.md   
```

### Versions

If you want to use versions, which can be useful for [documentation](#) for example, you'll have to create directories for each version.

```
└── content/
    └── docs/
        ├── v2.0/
        │   ├── getting-started.md
        │   └── index.md
        └── v1.0/
            ├── getting-started.md
            └── index.md
```
### Multilingual content

If your app needs to be in multiple languages, just put your md files in a sub directory:
```
└── content/
    └── posts/
        ├── en/
        │   ├── how-to-write-proper-markdown.md
        │   ├── obsidian-for-developers.md
        │   └── adding-custom-livewire-in-filament.md
        └── fr/
            ├── how-to-write-proper-markdown.md
            ├── obsidian-for-developers.md
            └── adding-custom-livewire-in-filament.md
```
### Mixing all

You can mix all of that, different types of content, translations, and versions:
```
└── content/
    ├── docs/
    │   ├── v2.0/
    │   │   ├── en/
    │   │   │   ├── getting-started.md
    │   │   │   └── index.md
    │   │   └── fr/
    │   │       ├── getting-started.md
    │   │       └── index.md
    │   └── v1.0/
    │       ├── en/
    │       │   ├── getting-started.md
    │       │   └── index.md
    │       └── fr/
    │           ├── getting-started.md
    │           └── index.md
    ├── releases/
    │   ├── v2.0.md
    │   ├── v1.3.md
    │   ├── v1.2.md
    │   ├── v1.1.md
    │   └── v1.0.md
    └── posts/
        ├── en/
        │   ├── how-to-write-proper-markdown.md
        │   ├── obsidian-for-developers.md
        │   └── adding-custom-livewire-in-filament.md
        └── fr/
            ├── how-to-write-proper-markdown.md
            ├── obsidian-for-developers.md
            └── adding-custom-livewire-in-filament.md
```

## Using DB and files: a hybrid approach

It's even possible to **use database driven content alongside files based content**.

For example, you might want to write blog posts in Editor in your admin panel and store them in your database, and keep your documentation in `.md` files.

> Plume will automagically detect if your content is located in database or in files, starting by looking at the database, then check for files existence.

