<?php

namespace App\Console\Commands;

use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeOldLoginRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-old-login-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user login records older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = Carbon::now('UTC')->subDays(30);
        
        $count = UserLogin::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("Successfully deleted {$count} old login records.");
        
        return Command::SUCCESS;
    }
}
