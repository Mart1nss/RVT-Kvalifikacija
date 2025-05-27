<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    $this->command->info('Creating users...');
    
    $users = [];
    
    // Admin user - created earliest with fixed date
    $adminCreatedAt = Carbon::now()->subMonths(6);
    $users[] = [
      'name' => 'admin',
      'email' => 'admin@example.com',
      'password' => Hash::make('123123123'),
      'usertype' => 'admin',
      'created_at' => $adminCreatedAt,
      'updated_at' => $adminCreatedAt,
    ];
    
    // Regular test user - created a bit later with fixed date
    $userCreatedAt = Carbon::now()->subMonths(5);
    $users[] = [
      'name' => 'user',
      'email' => 'user@example.com',
      'password' => Hash::make('123123123'),
      'usertype' => 'user',
      'created_at' => $userCreatedAt,
      'updated_at' => $userCreatedAt,
    ];
    
    // Regular users with random creation dates
    $otherUsers = [
      ['name' => 'alex', 'email' => 'alex@example.com'],
      ['name' => 'sam', 'email' => 'sam@example.com'],
      ['name' => 'max', 'email' => 'max@example.com'],
      ['name' => 'robin', 'email' => 'robin@example.com'],
      ['name' => 'chris', 'email' => 'chris@example.com'],
      ['name' => 'jamie', 'email' => 'jamie@example.com'],
      ['name' => 'taylor', 'email' => 'taylor@example.com'],
      ['name' => 'jordan', 'email' => 'jordan@example.com'],
      ['name' => 'casey', 'email' => 'casey@example.com'],
      ['name' => 'pat', 'email' => 'pat@example.com'],
      ['name' => 'jen', 'email' => 'jen@example.com'],
      ['name' => 'morgan', 'email' => 'morgan@example.com'],
      ['name' => 'kim', 'email' => 'kim@example.com'],
      ['name' => 'lee', 'email' => 'lee@example.com'],
    ];
    
    // Create each regular user with a random creation date
    foreach ($otherUsers as $user) {
      // Random date between 5 months ago and now
      $createdAt = Carbon::now()->subMonths(5)->addSeconds(rand(0, 5 * 30 * 24 * 60 * 60));
      
      $users[] = [
        'name' => $user['name'],
        'email' => $user['email'],
        'password' => Hash::make('123123123'),
        'usertype' => 'user',
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
      ];
    }

    // Insert all users into database
    foreach ($users as $user) {
      DB::table('users')->insert($user);
    }
    
    $this->command->info('Created ' . count($users) . ' users successfully!');
  }
}