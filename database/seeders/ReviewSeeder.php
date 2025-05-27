<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    /**
     * Review content samples
     */
    protected $reviewContents = [
        "This book completely changed my perspective. The author presents complex ideas in an accessible way that anyone can understand.",
        "A solid read with plenty of practical advice. I've already started implementing some of the strategies discussed.",
        "I found this book to be eye-opening. The concepts presented have helped me improve both personally and professionally.",
        "While the book starts strong, it loses momentum in the middle chapters. Still worth reading for the insights in the first half.",
        "An absolute must-read for anyone interested in this topic. The author's expertise shines through on every page.",
        "I appreciated the real-world examples that make the concepts easier to apply. Very well-researched and presented.",
        "This book exceeded my expectations. The writing style is engaging and the information is immediately applicable.",
        "A good introduction to the subject, though readers already familiar with the topic might find it a bit basic.",
        "I couldn't put this book down! The author has a gift for explaining complex topics in a relatable way.",
        "The practical exercises at the end of each chapter really helped me internalize the material. Highly recommended."
    ];

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
        
        $this->command->info('Creating reviews for existing books...');
        
        // Add between 3-8 reviews for each book
        foreach ($books as $book) {
            $reviewCount = rand(3, 8);
            
            // Get a random subset of users to leave reviews
            $reviewers = $users->random(min($reviewCount, $users->count()));
            
            foreach ($reviewers as $user) {
                // Random score between 3-5 (mostly positive reviews)
                $score = rand(3, 5);
                // For some variation, occasionally add lower scores
                if (rand(1, 10) == 1) {
                    $score = rand(1, 2);
                }
                
                // Random content
                $content = $this->reviewContents[array_rand($this->reviewContents)];
                
                // Add a review date in the past
                $reviewDate = Carbon::now()->subDays(rand(1, 60));
                
                DB::table('reviews')->insert([
                    'review_score' => $score,
                    'review_text' => $content,
                    'user_id' => $user->id,
                    'product_id' => $book->id,
                    'created_at' => $reviewDate,
                    'updated_at' => $reviewDate,
                ]);
            }
        }
        
        $this->command->info('Reviews created successfully!');
    }
} 