<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PruneAuditLogs extends Command
{
  protected $signature = 'audit:prune {--days=7}';
  protected $description = 'Prune audit logs older than specified days (default: 7 days)';

  public function handle()
  {
    try {
      $days = $this->option('days');
      $date = Carbon::now()->subDays($days);

      $count = 0;
      AuditLog::where('created_at', '<', $date)
        ->chunk(1000, function ($logs) use (&$count) {
          foreach ($logs as $log) {
            $log->delete();
            $count++;
          }
        });

      Log::info("Audit logs pruned successfully", [
        'deleted_count' => $count,
        'before_date' => $date->toDateTimeString(),
        'retention_days' => $days
      ]);

      $this->info("Successfully deleted {$count} old audit logs.");
    } catch (\Exception $e) {
      Log::error("Error pruning audit logs", [
        'error' => $e->getMessage()
      ]);
      $this->error("Error pruning audit logs: " . $e->getMessage());
    }
  }
}