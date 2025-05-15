<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Support\Facades\Notification as NotificationFacade; // Alias to avoid conflict
use App\Notifications\TicketNotification; // Assuming this is your main notification class for tickets
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PruneOldTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:prune-old 
                            {--P|period=30 : The period to prune: 7 (days), 30 (days), or "all" for all closed tickets.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune (delete) old, closed tickets and their associated data based on a specified period.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period');
        $validPeriods = ['7', '30', 'all'];

        if (!in_array(strtolower($period), $validPeriods)) {
            $this->error('Invalid period specified. Please use 7, 30, or "all".');
            return 1;
        }

        $this->info("Starting to prune closed tickets for period: {$period}");

        $query = Ticket::where('status', Ticket::STATUS_CLOSED);

        if (strtolower($period) === 'all') {
            if (!$this->confirm('Are you sure you want to delete ALL closed tickets, regardless of age? This action cannot be undone.')) {
                $this->info('Pruning cancelled by user.');
                return 0;
            }
        } else {
            $days = intval($period);
            $cutoffDate = Carbon::now()->subDays($days);
            $query->whereNotNull('resolved_at')->where('resolved_at', '<=', $cutoffDate);
            $this->info("Pruning tickets resolved on or before: {$cutoffDate->toDateString()}");
        }
        
        $oldClosedTickets = $query->get();
        $prunedCount = 0;

        if ($oldClosedTickets->isEmpty()) {
            $this->info('No closed tickets found matching the criteria to prune.');
            return 0;
        }

        $this->info("Found {$oldClosedTickets->count()} ticket(s) to prune.");

        foreach ($oldClosedTickets as $ticket) {
            $this->line("Processing ticket ID: {$ticket->id} (Ticket #: {$ticket->ticket_id}) resolved at {$ticket->resolved_at}");

            try {
                // 1. Delete associated notifications
                \Illuminate\Notifications\DatabaseNotification::where('data->ticket_id', $ticket->id)
                    ->orWhere('data->id', $ticket->id) // Some notifications might use 'id'
                    ->delete();
                $this->line("- Deleted associated notifications for ticket ID: {$ticket->id}");

                // 2. Delete associated ticket responses
                TicketResponse::where('ticket_id', $ticket->id)->delete();
                $this->line("- Deleted associated responses for ticket ID: {$ticket->id}");

                // 3. Delete the ticket itself
                $ticket->delete();
                $this->line("- Deleted ticket ID: {$ticket->id}");

                $prunedCount++;
            } catch (\Exception $e) {
                $this->error("Error pruning ticket ID {$ticket->id}: " . $e->getMessage());
                Log::error("Error pruning ticket ID {$ticket->id}: " . $e->getMessage(), ['exception' => $e]);
            }
        }

        $this->info("Successfully pruned {$prunedCount} old, closed ticket(s).");
        return 0;
    }
}
