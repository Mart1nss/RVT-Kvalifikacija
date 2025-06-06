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
    Schema::create('ticket_responses', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('ticket_id')->unsigned()->nullable();
      $table->bigInteger('user_id')->unsigned()->nullable();
      $table->text('response', 1000);
      $table->boolean('is_admin_response')->default(0);
      $table->timestamps();

      $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('set null');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('ticket_responses');
  }
};