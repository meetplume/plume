This is the PRD - product requirements document. We don't like red tape, but understand planning is necessary, so let's keep as short as possible, without sacrificing clarity on what's to include.

Let's think of this PRD folder, the index and other files that may be bor here, as a draft we can edit/tinker, change etc.

After it's complete, we can move/adjust to the right place in the documentation.

# Plume Goal

The goal is to have an easy and flexible way to manage content in a Laravel application, in the most near Laravel approach.

It's a platform that can work:
	- Standalone: the full project is Plume, acting as a CMS
	- For a Laravel Project, Inertia/Livewire/API/etc. There will be some routes of the app where the Panel will be accessible
	- On a Laravel project that has a Filament panel, it can integrate such panel

It allows to store content in the Filesystem (FS), in the Database (DB), and in a custom way (for developers, example, from an Eloquent model, or external API).

The content has a body, and also attributes, that are previously defined, and persisted, in FS and DB.

Such content is displayed in `content` pages, and gets listed and browsed in a page for that as well.

There's a search mechanism, we can use to search content.

There are pages, not related to content. Standalone pages. Examples: homepage, about, contact

It's multilingual.

Content has `versions` (optional). One wide use case is for documentation, which typically there's a whole set of content for v1, v2, etc

The UI is based on Templates, that can be distributed with Composer AND Zip file.

It has SEO features

It has AI features
