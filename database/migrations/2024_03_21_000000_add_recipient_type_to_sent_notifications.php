<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('sent_notifications', function (Blueprint $table) {
      $table->string('recipient_type')->default('all')->after('message');
    });
  }

  public function down(): void
  {
    Schema::table('sent_notifications', function (Blueprint $table) {
      $table->dropColumn('recipient_type');
    });
  }
};