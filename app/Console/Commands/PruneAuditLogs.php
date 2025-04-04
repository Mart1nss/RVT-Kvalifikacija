<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneAuditLogs extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'audit:prune {--days=7 : Number of days to keep logs}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Prune old audit logs based on retention period';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $days = $this->option('days');
    $this->info("Pruning audit logs older than {$days} days...");

    $cutoffDate = Carbon::now()->subDays($days);
    $count = AuditLog::where('created_at', '<', $cutoffDate)->count();

    if ($count === 0) {
      $this->info('No audit logs to prune.');
      return 0;
    }

    // Delete old logs
    $deleted = AuditLog::where('created_at', '<', $cutoffDate)->delete();

    $this->info("Successfully pruned {$deleted} audit logs.");
    Log::info("Pruned {$deleted} audit logs older than {$days} days.");

    return 0;
  }
}