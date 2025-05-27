<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating book records...');
        
        // Books from database screenshot
        $books = [
            [
                'id' => 1,
                'title' => 'Daily Mindset Boost',
                'author' => 'Harper Sullivan',
                'category_id' => 9,
                'file' => 'daily-mindset-boost_1748375234.pdf',
                'created_at' => '2025-05-27 19:47:14',
                'updated_at' => '2025-05-27 19:47:14',
            ],
            [
                'id' => 2,
                'title' => 'Echoes of the Past',
                'author' => 'Jamie Preston',
                'category_id' => 9,
                'file' => 'echoes-of-the-past_1748375258.pdf',
                'created_at' => '2025-05-27 19:47:38',
                'updated_at' => '2025-05-27 19:47:38',
            ],
            [
                'id' => 3,
                'title' => 'Work Smarter',
                'author' => 'Skyler Jameson',
                'category_id' => 8,
                'file' => 'work-smarter_1748375272.pdf',
                'created_at' => '2025-05-27 19:47:52',
                'updated_at' => '2025-05-27 19:47:52',
            ],
            [
                'id' => 4,
                'title' => 'Love & Logic',
                'author' => 'Dana Vaughn',
                'category_id' => 8,
                'file' => 'love-logic_1748375284.pdf',
                'created_at' => '2025-05-27 19:48:04',
                'updated_at' => '2025-05-27 19:48:04',
            ],
            [
                'id' => 5,
                'title' => 'History Unveiled',
                'author' => 'Sasha Quinn',
                'category_id' => 7,
                'file' => 'history-unveiled_1748375297.pdf',
                'created_at' => '2025-05-27 19:48:17',
                'updated_at' => '2025-05-27 19:48:17',
            ],
            [
                'id' => 6,
                'title' => 'The Negotiator\'s Edge',
                'author' => 'Remy Anderson',
                'category_id' => 6,
                'file' => 'the-negotiators-edge_1748375312.pdf',
                'created_at' => '2025-05-27 19:48:32',
                'updated_at' => '2025-05-27 19:48:32',
            ],
            [
                'id' => 7,
                'title' => 'Climbing the Ladder',
                'author' => 'Morgan Bennett',
                'category_id' => 6,
                'file' => 'climbing-the-ladder_1748375336.pdf',
                'created_at' => '2025-05-27 19:48:56',
                'updated_at' => '2025-05-27 19:48:56',
            ],
            [
                'id' => 8,
                'title' => 'The Wealth Formula',
                'author' => 'Casey Walker',
                'category_id' => 5,
                'file' => 'the-wealth-formula_1748375390.pdf',
                'created_at' => '2025-05-27 19:49:50',
                'updated_at' => '2025-05-27 19:49:50',
            ],
            [
                'id' => 9,
                'title' => 'The Calm Mind',
                'author' => 'Riley Hughes',
                'category_id' => 5,
                'file' => 'the-calm-mind_1748375419.pdf',
                'created_at' => '2025-05-27 19:50:19',
                'updated_at' => '2025-05-27 19:50:19',
            ],
            [
                'id' => 10,
                'title' => 'Think & Grow Simple',
                'author' => 'Reese Martin',
                'category_id' => 4,
                'file' => 'think-grow-simple_1748375445.pdf',
                'created_at' => '2025-05-27 19:50:45',
                'updated_at' => '2025-05-27 19:50:45',
            ],
            [
                'id' => 11,
                'title' => 'Soul Journeys',
                'author' => 'Cory Lawrence',
                'category_id' => 4,
                'file' => 'soul-journeys_1748375457.pdf',
                'created_at' => '2025-05-27 19:50:57',
                'updated_at' => '2025-05-27 19:50:57',
            ],
            [
                'id' => 12,
                'title' => 'Winning Conversations',
                'author' => 'Jules Donovan',
                'category_id' => 4,
                'file' => 'winning-conversations_1748375474.pdf',
                'created_at' => '2025-05-27 19:51:14',
                'updated_at' => '2025-05-27 19:51:14',
            ],
            [
                'id' => 13,
                'title' => 'Pathways to Change',
                'author' => 'Alex Ramirez',
                'category_id' => 3,
                'file' => 'pathways-to-change_1748375491.pdf',
                'created_at' => '2025-05-27 19:51:31',
                'updated_at' => '2025-05-27 19:51:31',
            ],
            [
                'id' => 14,
                'title' => 'Fit for Life',
                'author' => 'Taylor Knox',
                'category_id' => 3,
                'file' => 'fit-for-life_1748375503.pdf',
                'created_at' => '2025-05-27 19:51:43',
                'updated_at' => '2025-05-27 19:51:43',
            ],
            [
                'id' => 15,
                'title' => 'Focus Forward',
                'author' => 'Sam Taylor',
                'category_id' => 3,
                'file' => 'focus-forward_1748375517.pdf',
                'created_at' => '2025-05-27 19:51:57',
                'updated_at' => '2025-05-27 19:51:57',
            ],
            [
                'id' => 16,
                'title' => 'The Art of Focus',
                'author' => 'Avery Carter',
                'category_id' => 2,
                'file' => 'the-art-of-focus_1748375534.pdf',
                'created_at' => '2025-05-27 19:52:14',
                'updated_at' => '2025-05-27 19:52:14',
            ],
            [
                'id' => 17,
                'title' => 'Mastering the Deal',
                'author' => 'Jordan Lee Myers',
                'category_id' => 2,
                'file' => 'mastering-the-deal_1748375552.pdf',
                'created_at' => '2025-05-27 19:52:32',
                'updated_at' => '2025-05-27 19:52:32',
            ],
            [
                'id' => 18,
                'title' => 'Communicate to Connect',
                'author' => 'Phoenix Everett',
                'category_id' => 1,
                'file' => 'communicate-to-connect_1748375564.pdf',
                'created_at' => '2025-05-27 19:52:44',
                'updated_at' => '2025-05-27 19:52:44',
            ],
            [
                'id' => 19,
                'title' => 'Financial Fitness',
                'author' => 'Rowan Nelson',
                'category_id' => 1,
                'file' => 'financial-fitness_1748375577.pdf',
                'created_at' => '2025-05-27 19:52:57',
                'updated_at' => '2025-05-27 19:52:57',
            ],
            [
                'id' => 20,
                'title' => 'Inner Peace Unlocked',
                'author' => 'Kai Thompson',
                'category_id' => 1,
                'file' => 'inner-peace-unlocked_1748375598.pdf',
                'created_at' => '2025-05-27 19:53:18',
                'updated_at' => '2025-05-27 19:53:18',
            ],
            [
                'id' => 21,
                'title' => 'test file',
                'author' => 'admin',
                'category_id' => 10,
                'file' => 'test-file_1748376117.pdf',
                'created_at' => '2025-05-27 20:01:57',
                'updated_at' => '2025-05-27 20:01:57',
            ],
        ];
        
        // Insert all books
        foreach ($books as $book) {
            DB::table('products')->insert($book);
        }
        
        $this->command->info('Books seeded successfully! Now you can place your PDF files in storage/app/books and thumbnails in public/book-thumbnails');
    }
} 