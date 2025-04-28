<?php

namespace App\Console\Commands;

use App\Models\UserLogin;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Keep only the latest 20 login records per user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = 0;
        
        // Get all users with login records
        $userIds = UserLogin::select('user_id')
            ->distinct()
            ->pluck('user_id');
            
        foreach ($userIds as $userId) {
            // Get login IDs to delete (all except the latest 20)
            $loginIdsToKeep = UserLogin::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->pluck('id');
                
            // Delete all login records except the latest 20
            $deleted = UserLogin::where('user_id', $userId)
                ->whereNotIn('id', $loginIdsToKeep)
                ->delete();
                
            $deletedCount += $deleted;
        }
        
        $this->info("Successfully deleted {$deletedCount} old login records, keeping the latest 20 per user.");
        
        return Command::SUCCESS;
    }
}
