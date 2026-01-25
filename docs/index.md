# Plume

A Markdown tool for content - pages, docs, wikis and more.

## Introduction

This documentation is for development.

Plume is a package that allows to conveniently display content in various scopes, like a page in a route or a documentation folder.

### Testing the Application in a real context, with UI, etc

At root level, it's the code for the package itself, distributed with Packagist.
In the `playground` directory, there's a complete Laravel application that uses the package like we would on a real Laravel project. It's a `laravel new` without any starter kit - the most plain and simple, only Blade.
It's located in the `playground` directory. Differences from a real project:
- Includes the package via symlink (`composer.repositories`).
- It does not ignore the `.env` file.
  It's optimized to run tests and experiment with the package in a real context, with UI, etc.
  The Vite development server is run from this package root, but serves the playground app.

### Development workflow
- Run Vite server: `npm run dev` from this package root.
- Open the playground app in Herd, from the `playground` directory.
- Make changes to the package code, and see them reflected in the playground app.

🚀 Run the entire test suite:
```bash
composer test
```
