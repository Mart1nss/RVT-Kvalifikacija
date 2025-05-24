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
    Schema::create('sent_notifications', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
      $table->text('message', 250);
      $table->string('recipient_type')->default('all');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sent_notifications');
  }
};