<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $tickets = DB::table('tickets')->orderBy('id')->get();
        $counter = 1;

        foreach ($tickets as $ticket) {
            DB::table('tickets')
                ->where('id', $ticket->id)
                ->update(['ticket_id' => '#' . $counter]);
            $counter++;
        }
    }

    public function down()
    {
        // No need for down migration as this is a one-time data update
    }
};
