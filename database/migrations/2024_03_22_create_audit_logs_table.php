<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('audit_logs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('admin_id')->constrained('users');
      $table->string('action');
      $table->string('action_type'); // user, book, category, notification
      $table->string('description');
      $table->string('affected_item_id')->nullable();
      $table->string('affected_item_name')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('audit_logs');
  }
};