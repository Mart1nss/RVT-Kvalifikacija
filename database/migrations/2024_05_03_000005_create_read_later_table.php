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
    Schema::create('read_later', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->unsigned();
      $table->bigInteger('product_id')->unsigned();
      $table->timestamps();

      // Foreign Key Constraints - CASCADE DELETE
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

      // Unique Constraint
      $table->unique(['user_id', 'product_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('read_later');
  }
};