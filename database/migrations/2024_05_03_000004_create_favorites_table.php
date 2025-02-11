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
    Schema::create('favorites', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->unsigned(); // Foreign key, NOT nullable
      $table->bigInteger('product_id')->unsigned(); // Foreign key, NOT nullable
      $table->timestamps();

      // Foreign Key Constraints - CASCADE DELETE
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('favorites');
  }
};