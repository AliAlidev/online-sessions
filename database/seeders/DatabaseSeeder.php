<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()->call(RoleSeeder::class);
        app()->call(PermissionSeeder::class);
        app()->call(UserSeeder::class);
    }
}
