---
title: Development workflow
description: "Setting up, running the dev server, and the test suite."
---

# Development workflow

## First-time setup

```bash
composer setup
```

That runs, in order:

1. `composer install` for the package.
2. `rm -rf dist`.
3. `npm ci` for the package's frontend.
4. `cd playground && composer install`.
5. `rm -rf playground/public/vendor/plume`.
6. `cd playground && npm ci && npm run build`.

It assumes you serve the playground via Herd at <https://playground.test>. If you prefer `artisan serve`, run it from inside `playground/`.

## Running the dev server

```bash
composer dev
```

That opens <https://playground.test> and starts Vite (`npm run dev`) **from the package root** — the Vite config writes `playground/public/hot` so the playground's Blade picks up the dev server automatically.

You can also run Vite directly:

```bash
npm run dev
```

The Solo process `npm:dev` is registered in `solo.yml` (with `auto_start: false`).

## Test suite

```bash
composer test
```

Runs, in order:

- `test:lint` — `pint --parallel --test`, `npm run format:check`, `npm run lint:check`.
- `test:type-coverage` — `pest --type-coverage --exactly=100`.
- `test:unit` — `pest --exactly=100`.
- `test:refactor` — `rector --dry-run`.
- `cd playground && composer test` — the playground's own suite.

To auto-fix style and refactor issues:

```bash
composer test:fix
```

## Code style

The project uses **Laravel Pint**. Before committing:

```bash
vendor/bin/pint --dirty --format agent
```

PHP rules of note (Laravel Boost guidelines apply):

- Always use explicit return types and parameter types.
- Always curly braces for control structures, even one-liners.
- Use constructor property promotion in `__construct()`.
- Prefer PHPDoc blocks over inline comments. Avoid inline comments unless something is genuinely subtle.
- Enum keys in TitleCase.

## Tests

- All tests are written in **Pest 4**, in `tests/Unit/` and `tests/Feature/`.
- Package tests use **Orchestra Testbench** (since 0.6.0).
- Run a single file: `php artisan test --compact tests/Feature/ServiceProviderTest.php`.
- Filter by name: `php artisan test --compact --filter=testName`.

::: tip
Every change should be covered by a test — either a new one or an update to an existing one. Type coverage is enforced at 100%.
:::
