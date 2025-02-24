<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class UncategorizedCategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(
            ['name' => 'Uncategorized'],
            [
                'is_public' => false,
                'is_system' => true
            ]
        );
    }
} 