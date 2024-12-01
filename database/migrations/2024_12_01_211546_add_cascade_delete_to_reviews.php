<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['user_id']);
            
            // Add the foreign key back with cascade delete
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop the cascading foreign key
            $table->dropForeign(['user_id']);
            
            // Add back the original foreign key without cascade
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }
};
