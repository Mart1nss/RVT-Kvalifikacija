<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('users', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name', 255);
      $table->string('email', 255)->unique(); // Add unique constraint
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password', 255);
      $table->string('usertype', 255)->default('user'); // Default value
      $table->string('remember_token', 100)->nullable();
      $table->timestamps(); // created_at and updated_at, both nullable
      $table->timestamp('last_online')->nullable();
      $table->boolean('has_genre_preference_set')->default(0); // TINYINT(1) maps to boolean
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};