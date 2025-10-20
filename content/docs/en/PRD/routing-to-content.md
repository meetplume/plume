# Routing to content

Plume needs to be able to route to content in an easy and flexible way.

The routing must be configured.

To be hybrid, the `Content` definitions are Database-first, but can be defined in the Filesystem as well.

Example: (This example is just an approximation, we have to study the best way to define such routing)

```php
Route::content(
    key: 'docs',
    type: 'files',
    folder: base_path('content/docs'),
    multilingual: true,
    versions: ['v1', 'v2', 'v3'],
    defaultVersion: 'v3',
    routePrefix: 'docs',
    template: 'documentation',
);
```

In the Database UI, we can also define `Content`, in a no-code fashion. If we do so, the parameters are the same.
If we register the same `key` in both Filesystem and Database, the Database definition takes precedence.
