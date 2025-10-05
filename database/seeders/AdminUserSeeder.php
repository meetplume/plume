<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public const string INITIAL_ADMIN_EMAIL = 'hello@example.com';

    /**
     * Install initial user with admin role.
     */
    public function run(): void
    {
        if (User::where('email', static::INITIAL_ADMIN_EMAIL)->exists()) {
            return;
        }

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => static::INITIAL_ADMIN_EMAIL,
        ]);

        $admin->roles()->updateOrCreate(['role' => Role::Admin]);
    }
}
