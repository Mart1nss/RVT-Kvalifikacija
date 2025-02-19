<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->unsignedBigInteger('last_read_book_id')->nullable();
      $table->foreign('last_read_book_id')->references('id')->on('products')->onDelete('set null');
    });
  }

  public function down()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign(['last_read_book_id']);
      $table->dropColumn('last_read_book_id');
    });
  }
};