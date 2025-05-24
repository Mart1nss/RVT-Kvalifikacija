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
    Schema::create('tickets', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id')->unsigned()->nullable();
      $table->string('title', 50);
      $table->string('category', 255);
      $table->text('description', 1000);
      $table->enum('status', ['open', 'in_progress', 'closed'])->default('open'); // ENUM
      $table->timestamps();
      $table->bigInteger('assigned_admin_id')->unsigned()->nullable();
      $table->bigInteger('resolved_by')->unsigned()->nullable();
      $table->timestamp('resolved_at')->nullable();


      // Foreign Key Constraints
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
      $table->foreign('assigned_admin_id')->references('id')->on('users')->onDelete('set null');
      $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');

    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tickets');
  }
};
