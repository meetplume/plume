<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        $users = User::factory(10)->create();

        $categories = Category::factory(10)->create();

        $tags = Tag::factory(20)->create();

        $adminUser = User::query()
            ->where('email', AdminUserSeeder::INITIAL_ADMIN_EMAIL)
            ->firstOrFail();

        Post::factory(30)
            ->for($adminUser, 'author')
            ->withCategories($categories)
            ->withTags($tags)
            ->withComments(recycledUsers: $users)
            ->create();
    }
}
