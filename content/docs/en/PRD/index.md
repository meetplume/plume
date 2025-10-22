This is the PRD - product requirements document. We don't like red tape, but understand planning is necessary, so let's
keep as short as possible, without sacrificing clarity on what's to include.

Let's think of this PRD folder, the index and other files that may be bor here, as a draft we can edit/tinker, change
etc.

After it's complete, we can move/adjust to the right place in the documentation.

# Plume Goal

The goal is to have an easy and flexible way to manage content in a Laravel application, in the most near Laravel
approach.

## Operation mode

It's a platform that can work:

- Standalone: the full project is Plume, acting as a CMS
- For a Laravel Project, Inertia/Livewire/API/etc. There will be some routes of the app where the Panel will be
  accessible
- On a Laravel project that has a Filament panel, it can integrate such panel

## Storage

- It allows to store content in the Filesystem (FS), in the Database (DB), and in a custom way (for developers, example,
  from an Eloquent model, or external API).
  - Routing to content is flexible. Check the [Routing to content](routing-to-content.md) document for more details.
  - Routing to database
  - Routing to custom way (e.g. from an Eloquent model, or external API) (1 day??)
  
## Content

Content can be [multilingual](multilingual.md).

It has [SEO features](seo.md).

It can have comments (moderated)

### Collections

The content has a body, and also attributes, that are previously defined, and persisted, in FS and DB.

Such content is displayed in `content` pages, and gets listed and browsed in a page for that as well.

There's a [search mechanism](search.md), we can use to search content.

#### Presets

- Posts
- Links
- Releases (changelog)

### Pages

There are pages, not related to content. Standalone pages. Examples: homepage, about, contact.

### Docs

Documentation is specific type of content. For that reason, it's a resource of its own.

Documentation can have `sections` (optional). Ex: `Core`, `Drivers`, etc.

Documentation can have `versions` (optional). Ex: `v1.0`, `v2.0`, or `2001`, `2020`, or `tahoe`, `sonoma`, etc.

## Templates

The UI is based on Templates, that can be distributed with Composer AND Zip file.

- What are templates? (Content, Error pages, etc.)
- How to connect content to templates?
- How to create or customize a template?

## Menus

It has menu builder to create main menu, footer menu, etc.

## Forms

We can make basic forms, like contact form, or newsletter form.

## AI

It has AI features:

- Redact with AI
- Fix typos, grammar, etc.
- Translate with AI
- Generate images with AI (1 day??)

## Analytics

It has analytics features:
- Track usage for each type of content (impressions, clicks, hovers)
- Embed external analytics (Plausible, Matomo, etc.)

## Settings

It has settings to customize the behavior of the platform.

## For the future

- Revisions
- Private content
- Share collection items on social media
- RSS feed
- Portfolio preset
