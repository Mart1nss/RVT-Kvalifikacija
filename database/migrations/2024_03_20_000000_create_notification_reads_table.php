<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('notification_reads', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('sent_notification_id')->constrained()->onDelete('cascade');
      $table->timestamp('read_at');
      $table->timestamps();

      // Ensure a user can only have one read record per notification
      $table->unique(['user_id', 'sent_notification_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('notification_reads');
  }
};