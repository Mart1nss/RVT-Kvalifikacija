<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;
use App\Models\ReadBook;

class NoteSeeder extends Seeder
{
    /**
     * Sample notes to be added to books
     */
    protected $sampleNotes = [
        "This section explains the core concept really well. I need to remember this.",
        "Important point about implementing this strategy in daily life.",
        "Great quote that summarizes the author's philosophy.",
        "This contradicts what I learned previously - interesting perspective!",
        "Practical technique I should try out this week.",
        "The author's three-step process seems very effective.",
        "This example really clarified the concept for me.",
        "Need to research this topic more deeply.",
        "Key insight about improving productivity.",
        "Reminds me of concepts from other books I've read.",
        "Surprising statistics about this phenomenon.",
        "The author's personal story adds credibility to their advice.",
        "This visualization technique seems promising.",
        "Important distinction between these two approaches.",
        "A memorable framework for decision-making."
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only add notes to books that users have read
        $readBooks = ReadBook::with(['user', 'product'])->get();
        
        if ($readBooks->isEmpty()) {
            $this->command->error('No read books found. Please run the ReadBookSeeder first.');
            return;
        }
        
        $this->command->info('Creating notes for books that users have read...');
        
        foreach ($readBooks as $readBook) {
            // 70% chance that a user adds notes to a book they've read
            if (rand(1, 10) <= 7) {
                // Add between 1-5 notes per book
                $noteCount = rand(1, 5);
                
                for ($i = 0; $i < $noteCount; $i++) {
                    // Random note from sample notes
                    $noteText = $this->sampleNotes[array_rand($this->sampleNotes)];
                    
                    // Random page number between 1-300
                    $pageNumber = rand(1, 300);
                    
                    // Date after the read date but before now
                    $readDate = new Carbon($readBook->completed_at);
                    $daysSinceRead = max(1, Carbon::now()->diffInDays($readDate));
                    $noteDate = $readDate->copy()->addDays(rand(1, $daysSinceRead));
                    
                    DB::table('notes')->insert([
                        'note_text' => $noteText,
                        'user_id' => $readBook->user_id,
                        'product_id' => $readBook->product_id,
                        'book_title' => null,
                        'book_author' => null,
                        'created_at' => $noteDate,
                        'updated_at' => $noteDate,
                    ]);
                }
            }
        }
        
        $this->command->info('Notes created successfully!');
    }
} 