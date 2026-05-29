<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function (): void {
    File::deleteDirectory(app_path('Plume'));
    File::deleteDirectory(base_path('content'));
});

it('scaffolds a starter vault class and content index', function (): void {
    $this->artisan('plume:vault', ['name' => 'Blog'])
        ->assertSuccessful();

    $classPath = app_path('Plume/BlogVault.php');
    $contentPath = base_path('content/blog/index.md');

    expect(File::exists($classPath))->toBeTrue()
        ->and(File::exists($contentPath))->toBeTrue();

    expect(File::get($classPath))
        ->toContain('namespace App\Plume;')
        ->toContain('class BlogVault extends Vault')
        ->toContain("protected string \$prefix = '/blog';")
        ->toContain("protected string \$path = 'content/blog';")
        ->toContain('Discovery::Auto');

    expect(File::get($contentPath))
        ->toContain('title: Blog')
        ->toContain('Hello from Blog!');
});

it('derives kebab paths from a multi-word name', function (): void {
    $this->artisan('plume:vault', ['name' => 'BlogPosts'])
        ->assertSuccessful();

    expect(File::exists(app_path('Plume/BlogPostsVault.php')))->toBeTrue()
        ->and(File::exists(base_path('content/blog-posts/index.md')))->toBeTrue();

    expect(File::get(app_path('Plume/BlogPostsVault.php')))
        ->toContain("protected string \$prefix = '/blog-posts';")
        ->toContain("protected string \$path = 'content/blog-posts';");
});

it('fails when the vault already exists', function (): void {
    $this->artisan('plume:vault', ['name' => 'Blog'])->assertSuccessful();

    $this->artisan('plume:vault', ['name' => 'Blog'])
        ->expectsOutputToContain('already exists')
        ->assertFailed();
});
