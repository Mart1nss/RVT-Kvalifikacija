<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // First, update existing statuses to match new format
            DB::statement("UPDATE tickets SET status = 'open' WHERE status = 'Open'");
            DB::statement("UPDATE tickets SET status = 'in_progress' WHERE status = 'In Progress'");
            DB::statement("UPDATE tickets SET status = 'resolved' WHERE status = 'Resolved'");
            
            // Then modify the column to use ENUM
            DB::statement("ALTER TABLE tickets MODIFY status ENUM('open', 'in_progress', 'resolved') NOT NULL DEFAULT 'open'");
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }
};
