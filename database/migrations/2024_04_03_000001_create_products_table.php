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
      $table->string('title', 255);
      $table->string('author', 255);
      $table->bigInteger('category_id')->unsigned()->nullable(); // Foreign key, nullable
      $table->string('file', 255)->nullable();
      $table->timestamps(); // created_at and updated_at, nullable by default

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