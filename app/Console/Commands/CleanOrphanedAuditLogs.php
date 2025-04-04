<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanOrphanedAuditLogs extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'audit:clean-orphaned';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Clean up audit logs with invalid admin IDs';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $this->info('Cleaning orphaned audit logs...');

    // Get valid admin IDs
    $adminIds = User::where('usertype', 'admin')->pluck('id')->toArray();

    // Find orphaned logs
    $orphanedCount = AuditLog::whereNotIn('admin_id', $adminIds)->count();

    if ($orphanedCount === 0) {
      $this->info('No orphaned audit logs found.');
      return 0;
    }

    $this->info("Found {$orphanedCount} orphaned audit logs.");

    // Confirm deletion
    if (!$this->confirm('Do you want to delete these orphaned logs?')) {
      $this->info('Operation cancelled.');
      return 0;
    }

    // Delete orphaned logs
    $deleted = AuditLog::whereNotIn('admin_id', $adminIds)->delete();

    $this->info("Successfully deleted {$deleted} orphaned audit logs.");
    Log::info("Deleted {$deleted} orphaned audit logs via command.");

    return 0;
  }
}