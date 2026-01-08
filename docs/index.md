# Plume

An utility for markdown content - pages, docs, wikis and more.

## Introduction

This documentation is for development.

Plume is a package that allows to conveniently display content in various scopes, like a page in a route or a documentation folder.

### Testing the Application in a real context, with UI, etc

This repository includes a complete app that uses the package like we would on a real Laravel project. 
It's a `laravel new` without any starter kit - the most plain and simple, only Blade.
It's located in the `playground` directory. Differences from a real project:
  - Includes the package via symlink (`composer.repositories`).
  - It does not ignore the `.env` file
