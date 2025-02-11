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
    Schema::create('audit_logs', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('admin_id')->unsigned()->nullable(); //Nullable Foreign Key
      $table->string('action', 255);
      $table->string('action_type', 255);
      $table->string('description', 255);
      $table->string('affected_item_id', 255)->nullable();
      $table->string('affected_item_name', 255)->nullable();
      $table->timestamps(); // created_at and updated_at (nullable)

      // Foreign Key Constraint - SET NULL
      $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('audit_logs');
  }
};