<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
  public function run(): void
  {
    $categories = [
      [
        'id' => 1,
        'name' => 'Psychology',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 2,
        'name' => 'Sales & Negotiation',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 3,
        'name' => 'Productivity',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 4,
        'name' => 'Business & Career',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 5,
        'name' => 'Money & Investments',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 6,
        'name' => 'Health & Wellness',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 7,
        'name' => 'History',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 8,
        'name' => 'Relationships & Communication',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 9,
        'name' => 'Spirituality & Philosophy',
        'is_public' => true,
        'is_system' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 10,
        'name' => 'Uncategorized',
        'is_public' => false,
        'is_system' => true,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
    ];

    foreach ($categories as $category) {
      DB::table('categories')->insert($category);
    }
  }
}