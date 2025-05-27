<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;

class ReadBookSeeder extends Seeder
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
        
        $this->command->info('Marking books as read by users...');
        
        // Each user has read between 2-8 books
        foreach ($users as $user) {
            // How many books this user has read
            $readCount = rand(2, min(8, $books->count()));
            
            // Get random books for this user to have read
            $readBooks = $books->random($readCount);
            
            foreach ($readBooks as $book) {
                // Different completion dates in the past
                $completedDate = Carbon::now()->subDays(rand(1, 90));
                
                DB::table('read_books')->insert([
                    'user_id' => $user->id,
                    'product_id' => $book->id,
                    'created_at' => $completedDate,
                    'updated_at' => $completedDate,
                ]);
                
                // 50% chance that this is their last read book
                if (rand(0, 1) == 1) {
                    // Update user's last_read_book_id
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'last_read_book_id' => $book->id
                        ]);
                }
            }
        }
        
        $this->command->info('Books marked as read successfully!');
    }
} 