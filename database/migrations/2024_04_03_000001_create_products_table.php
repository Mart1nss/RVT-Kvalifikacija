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
    Schema::create('products', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('title', 100);
      $table->string('author', 50);
      $table->bigInteger('category_id')->unsigned()->nullable();
      $table->string('file', 255)->nullable();
      $table->timestamps();

      // Foreign key constraint
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};