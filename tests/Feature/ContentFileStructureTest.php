<?php

use App\Models\ContentFile;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

beforeEach(function () {
    Config::set('content.path', base_path('tests/content'));
    $this->contentPath = config('content.path');
    $this->contentFiles = ContentFile::all();
});


test('all docs files with versions and languages are accessible', function () {
    $files = $this->contentFiles;

    // Test all version and language combinations
    $versions = ['v2.0', 'v1.0'];
    $languages = ['en', 'fr'];

    foreach ($versions as $version) {
        foreach ($languages as $language) {
            // Test specific page
            $file = $files->first(fn ($f) => $f->isPath("/docs/{$version}/getting-started", $language));
            expect($file)->not->toBeNull()
                ->and($file->relativePathname)->toBe("docs/{$version}/{$language}/getting-started.md");

            // Test index page
            $file = $files->first(fn ($f) => $f->isPath("/docs/{$version}", $language));
            expect($file)->not->toBeNull()
                ->and($file->relativePathname)->toBe("docs/{$version}/{$language}/index.md");
        }
    }
});

test('all release notes files are accessible', function () {
    $files = $this->contentFiles;

    // Test releases (no language subdirectory for releases)
    $releases = ['v2.0', 'v1.3', 'v1.2', 'v1.1', 'v1.0'];

    foreach ($releases as $version) {
        $file = $files->first(fn ($f) => $f->relativePathname === "releases/{$version}.md");
        expect($file)->not->toBeNull()
            ->and($file->relativePathname)->toBe("releases/{$version}.md");
    }
});

test('all posts with language subdirectories are accessible', function () {
    $files = $this->contentFiles;

    $postSlugs = [
        'how-to-write-proper-markdown',
        'obsidian-for-developers',
        'adding-custom-livewire-in-filament',
    ];

    // Test English posts
    foreach ($postSlugs as $slug) {
        $file = $files->first(fn ($f) => $f->isPath("/posts/{$slug}", 'en'));
        expect($file)->not->toBeNull()
            ->and($file->relativePathname)->toBe("posts/en/{$slug}.md");
    }

    // Test French posts
    foreach ($postSlugs as $slug) {
        $file = $files->first(fn ($f) => $f->isPath("/posts/{$slug}", 'fr'));
        expect($file)->not->toBeNull()
            ->and($file->relativePathname)->toBe("posts/fr/{$slug}.md");
    }
});

test('files are not accessible with wrong language', function () {
    $files = $this->contentFiles;

    // Try to access an English doc with French locale - should not find it
    $file = $files->first(fn ($f) => $f->isPath('/docs/v2.0/getting-started', 'de'));
    expect($file)->toBeNull();

    // Try to access a post that exists in 'en' but request it in 'de'
    $file = $files->first(fn ($f) => $f->isPath('/posts/how-to-write-proper-markdown', 'de'));
    expect($file)->toBeNull();
});

test('files are not accessible with wrong version', function () {
    $files = $this->contentFiles;

    // Try to access a v2.0 doc with v3.0 path - should not find it
    $file = $files->first(fn ($f) => $f->isPath('/docs/v3.0/getting-started', 'en'));
    expect($file)->toBeNull();

    // Try to access a v1.0 doc with v1.5 path - should not find it
    $file = $files->first(fn ($f) => $f->isPath('/docs/v1.5', 'fr'));
    expect($file)->toBeNull();
});

// ✅works in dev, but not in tests
test('docs routes are resolved correctly', function () {
        visit('/docs/v2.0')->assertSee('docs/v2.0/en/index.md');
        visit('/docs/v2.0/getting-started')->assertSee('docs/v2.0/en/getting-started.md');
        visit('/docs/v1.0')->assertSee('docs/v1.0/en/index.md');
        visit('/docs/v1.0/getting-started')->assertSee('docs/v1.0/en/getting-started.md');
        visit('/fr/docs/v2.0')->assertSee('docs/v2.0/fr/index.md');
        visit('/fr/docs/v2.0/getting-started')->assertSee('docs/v2.0/fr/getting-started.md');
        visit('/fr/docs/v1.0')->assertSee('docs/v1.0/fr/index.md');
        visit('/fr/docs/v1.0/getting-started')->assertSee('docs/v1.0/fr/getting-started.md');
})->skip('I do not know how to test LaravelLocalization::getNonLocalizedURL()');

test('releases routes are resolved correctly', function () {
    visit('releases/v2.0')->assertSee('releases/v2.0.md');
    visit('releases/v1.3')->assertSee('releases/v1.3.md');
    visit('releases/v1.2')->assertSee('releases/v1.2.md');
    visit('releases/v1.1')->assertSee('releases/v1.1.md');
    visit('releases/v1.0')->assertSee('releases/v1.0.md');
})->todo('implement releases routes');

test('posts routes are resolved correctly', function () {
    visit('blog/how-to-write-proper-markdown')->assertSee('posts/en/how-to-write-proper-markdown.md');
    visit('blog/obsidian-for-developers')->assertSee('posts/en/obsidian-for-developers.md');
    visit('blog/adding-custom-livewire-in-filament')->assertSee('posts/en/adding-custom-livewire-in-filament.md');
    visit('fr/blog/how-to-write-proper-markdown')->assertSee('posts/fr/how-to-write-proper-markdown.md');
    visit('fr/blog/obsidian-for-developers')->assertSee('posts/fr/obsidian-for-developers.md');
    visit('fr/blog/adding-custom-livewire-in-filament')->assertSee('posts/fr/adding-custom-livewire-in-filament.md');
})->todo('implement posts routes');
