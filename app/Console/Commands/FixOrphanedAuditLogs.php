<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixOrphanedAuditLogs extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'audit:fix-orphaned {admin_id? : ID of admin to assign orphaned logs to}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Fix orphaned audit logs by assigning them to a valid admin';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $this->info('Fixing orphaned audit logs...');

    // Get valid admin IDs
    $adminIds = User::where('usertype', 'admin')->pluck('id')->toArray();

    if (empty($adminIds)) {
      $this->error('No admin users found in the system.');
      return 1;
    }

    // Find orphaned logs
    $orphanedCount = AuditLog::whereNotIn('admin_id', $adminIds)->count();

    if ($orphanedCount === 0) {
      $this->info('No orphaned audit logs found.');
      return 0;
    }

    $this->info("Found {$orphanedCount} orphaned audit logs.");

    // Get admin ID to use
    $adminId = $this->argument('admin_id');

    // If no admin ID provided, list admins and ask user to choose
    if (!$adminId) {
      $admins = User::where('usertype', 'admin')->get(['id', 'name']);

      $this->info('Available admins:');
      foreach ($admins as $admin) {
        $this->line("ID: {$admin->id}, Name: {$admin->name}");
      }

      $adminId = $this->ask('Enter the ID of the admin to assign orphaned logs to:');
    }

    // Validate admin ID
    if (!in_array($adminId, $adminIds)) {
      $this->error('Invalid admin ID. Please provide a valid admin ID.');
      return 1;
    }

    // Confirm update
    if (!$this->confirm("Are you sure you want to assign all orphaned logs to admin ID {$adminId}?")) {
      $this->info('Operation cancelled.');
      return 0;
    }

    // Update orphaned logs
    $updated = AuditLog::whereNotIn('admin_id', $adminIds)->update(['admin_id' => $adminId]);

    $this->info("Successfully updated {$updated} orphaned audit logs.");
    Log::info("Fixed {$updated} orphaned audit logs by assigning them to admin ID {$adminId}.");

    return 0;
  }
}