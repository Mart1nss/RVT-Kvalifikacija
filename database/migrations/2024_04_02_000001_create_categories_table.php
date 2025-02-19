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
    Schema::create('categories', function (Blueprint $table) {
      $table->bigIncrements('id');  // BIGINT, AUTO_INCREMENT, Primary Key
      $table->string('name', 255); // VARCHAR with length 255
      $table->timestamps(); // Adds created_at and updated_at TIMESTAMP columns, both NULLABLE
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('categories');
  }
};