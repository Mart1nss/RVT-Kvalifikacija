<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run daily at midnight
        $schedule->command('audit:prune --days=' . config('audit.retention_days'))
            ->daily()
            ->at('00:00')
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Clean orphaned audit logs weekly
        $schedule->command('audit:clean-orphaned')
            ->weekly()
            ->sundays()
            ->at('01:00')
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        $schedule->command('app:purge-old-login-records')->daily();

        // Prune closed tickets daily
        $schedule->command('tickets:prune-old')
                 ->daily()
                 ->at('02:00')
                 ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        Commands\GenerateBookThumbnails::class,
        Commands\PruneOldTickets::class,
    ];
}
