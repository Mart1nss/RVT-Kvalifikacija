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
        Schema::table('forums', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['user_id']);

            // Modify the column to be nullable
            $table->foreignId('user_id')->nullable()->change();

            // Add the new foreign key constraint with onDelete('set null')
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });

        Schema::table('forum_replies', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['user_id']);

            // Modify the column to be nullable
            $table->foreignId('user_id')->nullable()->change();

            // Add the new foreign key constraint with onDelete('set null')
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            // Drop the 'set null' foreign key
            $table->dropForeign(['user_id']);

            // Revert the column to not nullable (assuming it was not nullable before)
            // Note: Reverting `change()` requires doctrine/dbal. If it was `constrained()`, it implies not nullable.
            $table->foreignId('user_id')->nullable(false)->change();
            
            // Re-add the original foreign key constraint with onDelete('cascade')
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('forum_replies', function (Blueprint $table) {
            // Drop the 'set null' foreign key
            $table->dropForeign(['user_id']);

            // Revert the column to not nullable
            $table->foreignId('user_id')->nullable(false)->change();

            // Re-add the original foreign key constraint with onDelete('cascade')
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
