<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class CreateUncategorizedCategory extends Command
{
    protected $signature = 'categories:create-uncategorized';
    protected $description = 'Creates the uncategorized category if it does not exist';

    public function handle()
    {
        $category = Category::firstOrCreate(
            ['name' => 'Uncategorized'],
            [
                'is_public' => false,
                'is_system' => true
            ]
        );

        if ($category->wasRecentlyCreated) {
            $this->info('Uncategorized category created successfully.');
        } else {
            $this->info('Uncategorized category already exists.');
        }

        return Command::SUCCESS;
    }
} 