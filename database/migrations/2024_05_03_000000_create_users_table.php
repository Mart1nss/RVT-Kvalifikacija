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
      $table->string('name', 10);
      $table->string('email', 255)->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password', 255);
      $table->string('usertype', 255)->default('user');
      $table->string('remember_token', 100)->nullable();
      $table->timestamps(); 
      $table->timestamp('last_online')->nullable();
      $table->boolean('has_genre_preference_set')->default(false);
      $table->unsignedBigInteger('last_read_book_id')->nullable();
      $table->foreign('last_read_book_id')
        ->references('id')
        ->on('products') 
        ->onDelete('set null');
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