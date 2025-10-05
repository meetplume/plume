# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - 2025-10-04

### Added

- Add `eneadm/ladder` package for role management.
- Panel access granted via Role.
- User resource page to manage admin Users.

### Changed

- Remove admin check via environment variable.
- Update .env.example APP_URL.

## [Unreleased] - 2025-10-05

### Added

- Install `calebporzio/sushi`.
  To enable read from files into Eloquent models (sqlite under the hood).
- Install `spatie/laravel-ray`.
  To enable debugging with Ray app.
- Install `spatie/yaml-front-matter`.
  To enable reading yaml front matter from markdown files.
- Add `content.show` route, controller and view, to present Collection items.
  - The view is just a POC, and should be customized further.
- Add ContentFile model, to read from files.
- Add `docs` collection with dummy data - in `/content`.
