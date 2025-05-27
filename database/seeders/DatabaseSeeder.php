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

        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            BookSeeder::class,
            ForumSeeder::class,
            ForumReplySeeder::class,
            // Run these after books are seeded:
            // ReviewSeeder::class,
            // ReadBookSeeder::class,
            // NoteSeeder::class,
            // FavoriteSeeder::class,
        ]);
    }
}
