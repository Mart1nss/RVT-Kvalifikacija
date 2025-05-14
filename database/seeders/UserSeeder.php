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
    $users = [
      [
        'name' => 'janis',
        'email' => 'janis@example.com',
        'password' => Hash::make('password123'),
        'usertype' => 'admin',
        'email_verified_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'name' => 'peters',
        'email' => 'peters@example.com',
        'password' => Hash::make('password123'),
        'usertype' => 'user',
        'email_verified_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'name' => 'user1',
        'email' => 'user1@example.com',
        'password' => Hash::make('password123'),
        'usertype' => 'user',
        'email_verified_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'name' => 'user2',
        'email' => 'user2@example.com',
        'password' => Hash::make('password123'),
        'usertype' => 'user',
        'email_verified_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
      [
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'usertype' => 'user',
        'email_verified_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ],
    ];

    foreach ($users as $user) {
      DB::table('users')->insert($user);
    }
  }
}