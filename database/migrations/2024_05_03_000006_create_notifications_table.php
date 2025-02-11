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
    Schema::create('notifications', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->unsigned()->nullable(); // Foreign key
      $table->text('message');
      $table->boolean('is_read')->default(0); // TINYINT(1) -> boolean, default 0
      $table->timestamps(); // created_at and updated_at (nullable)
      $table->string('link', 255)->nullable();

      // Foreign Key Constraint - SET NULL
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('notifications');
  }
};