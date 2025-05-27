<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Product::all();
        $users = User::where('usertype', 'user')->get();
        
        if ($books->isEmpty()) {
            $this->command->error('No books found in the database. Please upload books first.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->error('No users found in the database. Please run UserSeeder first.');
            return;
        }
        
        $this->command->info('Adding favorite books for users...');
        
        // Each user has 1-4 favorite books
        foreach ($users as $user) {
            // How many favorites this user has
            $favoriteCount = rand(1, min(4, $books->count()));
            
            // Get random books for this user to favorite
            $favoriteBooks = $books->random($favoriteCount);
            
            foreach ($favoriteBooks as $book) {
                // Random date in the past
                $favoriteDate = Carbon::now()->subDays(rand(1, 45));
                
                DB::table('favorites')->insert([
                    'user_id' => $user->id,
                    'product_id' => $book->id,
                    'created_at' => $favoriteDate,
                    'updated_at' => $favoriteDate,
                ]);
            }
        }
        
        $this->command->info('Favorite books added successfully!');
    }
} 