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
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 2,
        'name' => 'Sales & Negotiation',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 3,
        'name' => 'Productivity',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 4,
        'name' => 'Business & Career',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 5,
        'name' => 'Money & Investments',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 6,
        'name' => 'Health & Wellness',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 7,
        'name' => 'History',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 8,
        'name' => 'Relationships & Communication',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'id' => 9,
        'name' => 'Spirituality & Philosophy',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
    ];

    foreach ($categories as $category) {
      DB::table('categories')->insert($category);
    }
  }
}