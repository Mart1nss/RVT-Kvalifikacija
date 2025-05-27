<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForumReplySeeder extends Seeder
{
    public function run(): void
    {
        $replies = [
            // Replies for Forum 1 - Book Recommendations
            [
                'content' => 'I highly recommend "Atomic Habits" by James Clear. It completely changed how I approach habit formation and personal development.',
                'forum_id' => 1,
                'user_id' => 2, // user
                'created_at' => Carbon::now()->subDays(29),
                'updated_at' => Carbon::now()->subDays(29),
            ],
            [
                'content' => '"Mindset" by Carol Dweck was a game-changer for me. It helped me understand the importance of a growth mindset versus a fixed mindset.',
                'forum_id' => 1,
                'user_id' => 3, // alex
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'content' => 'For productivity, "Deep Work" by Cal Newport is essential reading. It taught me how to focus and get more meaningful work done.',
                'forum_id' => 1,
                'user_id' => 4, // sam
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(27),
            ],
            
            // Replies for Forum 2 - Daily Habits
            [
                'content' => 'Starting my day with a 10-minute meditation has been transformative. It helps me stay centered and focused throughout the day.',
                'forum_id' => 2,
                'user_id' => 5, // max
                'created_at' => Carbon::now()->subDays(24),
                'updated_at' => Carbon::now()->subDays(24),
            ],
            [
                'content' => 'I use time blocking in my calendar to ensure I make progress on important projects. It helps me avoid the trap of just responding to emails all day.',
                'forum_id' => 2,
                'user_id' => 6, // robin
                'created_at' => Carbon::now()->subDays(23),
                'updated_at' => Carbon::now()->subDays(23),
            ],
            [
                'content' => 'Reading for 30 minutes before bed has improved my sleep quality and helped me learn consistently. I avoid screens during this time.',
                'forum_id' => 2,
                'user_id' => 7, // chris
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            
            // Replies for Forum 3 - Mindfulness
            [
                'content' => 'Body scan meditation has been really helpful for me as a beginner. It helps me connect with physical sensations and stay present.',
                'forum_id' => 3,
                'user_id' => 8, // jamie
                'created_at' => Carbon::now()->subDays(19),
                'updated_at' => Carbon::now()->subDays(19),
            ],
            [
                'content' => 'I use the Headspace app for guided meditations. The structured courses have helped me build a consistent practice.',
                'forum_id' => 3,
                'user_id' => 9, // taylor
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'content' => 'Walking meditation is great for those who struggle with sitting still. Try focusing on each step and the sensations in your feet.',
                'forum_id' => 3,
                'user_id' => 10, // jordan
                'created_at' => Carbon::now()->subDays(17),
                'updated_at' => Carbon::now()->subDays(17),
            ],
            
            // Replies for Forum 4 - Procrastination
            [
                'content' => 'The Pomodoro Technique has been a game-changer for me. 25 minutes of focused work followed by a 5-minute break helps me stay on task.',
                'forum_id' => 4,
                'user_id' => 11, // casey
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'content' => 'I use the "2-minute rule" - if a task takes less than 2 minutes, I do it immediately instead of putting it off.',
                'forum_id' => 4,
                'user_id' => 12, // pat
                'created_at' => Carbon::now()->subDays(13),
                'updated_at' => Carbon::now()->subDays(13),
            ],
            [
                'content' => 'Breaking down large projects into smaller, manageable tasks has helped me overcome feeling overwhelmed and procrastinating.',
                'forum_id' => 4,
                'user_id' => 13, // jen
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            
            // Replies for Forum 5 - Financial Literacy
            [
                'content' => '"The Simple Path to Wealth" by JL Collins is an excellent introduction to index fund investing and financial independence.',
                'forum_id' => 5,
                'user_id' => 14, // morgan
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            [
                'content' => 'I found the r/personalfinance subreddit to be an invaluable resource. Their wiki has great structured advice for every financial stage.',
                'forum_id' => 5,
                'user_id' => 15, // kim
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'content' => 'Tracking my expenses with an app like YNAB (You Need A Budget) completely changed my relationship with money and helped me save more.',
                'forum_id' => 5,
                'user_id' => 16, // lee
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            
            // Replies for Forum 6 - Relationships
            [
                'content' => 'Learning about the "five love languages" helped me understand how different people express and receive love differently.',
                'forum_id' => 6,
                'user_id' => 1, // admin
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'content' => 'Active listening - fully focusing on the speaker without planning your response - has improved all my relationships.',
                'forum_id' => 6,
                'user_id' => 2, // user
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'content' => '"Nonviolent Communication" by Marshall Rosenberg offers a framework for compassionate communication that has helped me navigate difficult conversations.',
                'forum_id' => 6,
                'user_id' => 3, // alex
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($replies as $reply) {
            DB::table('forum_replies')->insert($reply);
        }
    }
} 