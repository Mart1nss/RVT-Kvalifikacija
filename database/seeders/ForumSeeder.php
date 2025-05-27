<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $forums = [
            [
                'title' => 'Book Recommendations for Personal Growth',
                'description' => 'Share and discuss your favorite self-improvement books. What books have made the biggest impact on your personal development journey? Looking for recommendations in specific areas like productivity, mindfulness, or career development.',
                'user_id' => 1, // admin
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'title' => 'Daily Habits for Success',
                'description' => 'Let\'s discuss effective daily habits that can lead to long-term success. Share your morning routines, productivity practices, and strategies for maintaining consistency with healthy habits. What small changes have made the biggest difference in your life?',
                'user_id' => 2, // user
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'title' => 'Mindfulness Meditation Techniques',
                'description' => 'A space to share different meditation techniques and mindfulness practices. How has meditation impacted your life? What techniques work best for beginners? Share your experiences and ask questions about developing a meditation practice.',
                'user_id' => 3, // alex
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Overcoming Procrastination',
                'description' => 'Strategies and techniques for beating procrastination and getting things done. Share your struggles and successes with procrastination. What methods have helped you stay focused and productive? Let\'s help each other develop better work habits.',
                'user_id' => 4, // sam
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'title' => 'Financial Literacy Resources',
                'description' => 'Discuss books, courses, and resources for improving financial literacy. What resources have helped you understand investing, saving, and building wealth? Share your journey toward financial independence and the lessons you\'ve learned along the way.',
                'user_id' => 5, // max
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Building Better Relationships',
                'description' => 'Discuss communication skills, emotional intelligence, and strategies for building healthier relationships in all areas of life. Share your experiences with improving relationships and ask for advice on specific relationship challenges.',
                'user_id' => 6, // robin
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($forums as $forum) {
            DB::table('forums')->insert($forum);
        }
    }
} 