# Why MingleJS?

This is very different from the conventional ways. We can pick any flavour of Livewire, Alpine, Vue, React, in the same app.

Of course, we can change combinations on a per-page basis.

## Why would we want this?

- You already know a flavour of JS and want to incrementally adopt Laravel/Livewire as a backend.
- Landing pages, marketing pages, etc. => Plenty of React/Vue components out there.
- JS ecosystem has an infinite amount of tools and libraries.
- Being on Blade, SSR for SEO is there right from the beginning.
- We want to use Vue and React, but I don't want to use SPA nor Inertia.
    - I want to use Blade and Livewire, while keeping the possibility to use Vue/React components.
- Having a Livewire component as a container, in our Vue/React component we get to call PHP functions simply by calling `$wire.method()`.
  - We don't have to write an Ajax request, a route, a controller, etc.
  - We can if we need, for some reason, but we don't have to.
