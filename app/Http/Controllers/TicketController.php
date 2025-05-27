<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Notification;
use App\Models\TicketResponse;
use App\Notifications\TicketNotification;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'active');

        $baseQuery = $user->isAdmin() ? Ticket::query() : $user->tickets();
        $query = $baseQuery->with(['user', 'assignedAdmin']);

        if ($tab === 'active') {
            $tickets = $query->whereIn('status', ['open', 'in_progress'])->latest()->get();
        } else {
            $tickets = $query->where('status', 'closed')->latest()->get();
        }

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required|max:50',
            'category' => 'required',
            'description' => 'required|max:1000'
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'title' => $validatedData['subject'],
            'category' => $validatedData['category'],
            'description' => $validatedData['description'],
            'status' => 'open'
        ]);

        // Paziņot visiem administratoriem par jauno biļeti
        $admins = User::where('usertype', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new TicketNotification(
                $ticket,
                'new_ticket',
                ['message' => "New support ticket #{$ticket->id} has been created"]
            ));
        }

        AuditLogService::log(
            "Created ticket",
            "ticket",
            "Created ticket #{$ticket->id}",
            $ticket->id,
            "Ticket creation"
        );

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $ticket->user_id) {
            abort(403);
        }
        $ticket->load(['user', 'assignedAdmin', 'responses.user', 'resolved_by_user']);

        return view('tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'status' => 'required|in:open,in_progress,closed'
        ]);

        $oldStatus = $ticket->status;

        // Atjaunina biļetes statusu un saistītos laukus
        $ticket->status = $validatedData['status'];
        if ($validatedData['status'] === 'closed') {
            $ticket->resolved_at = now();
            $ticket->resolved_by = auth()->id();
        }
        $ticket->save();

        // Ja biļete tiek pieņemta, maina statusu uz "in_progress" un dzēš jauno biļešu paziņojumus visiem administratoriem
        if ($validatedData['status'] === 'in_progress' && $oldStatus === 'open') {
            Log::info("[TicketController@updateStatus] Ticket ID {$ticket->id} accepted by Auth User: " . auth()->id() . ". Attempting to delete 'new_ticket' notifications for all admins.");
            $adminUsers = User::where('usertype', 'admin')->get();
            $adminUsers->each(function ($adminUser) use ($ticket) {
                $deletedCount = $adminUser->notifications()
                    ->where('type', 'App\Notifications\TicketNotification')
                    ->where('data->ticket_id', $ticket->id)
                    ->where('data->type', 'new_ticket')
                    ->delete();
                if ($deletedCount > 0) {
                    Log::info("[TicketController@updateStatus] Deleted 'new_ticket' notification for admin ID {$adminUser->id} regarding ticket ID {$ticket->id}.");
                }
            });
        }

        // Ja biļete tiek slēgta
        if ($validatedData['status'] === 'closed') {
            if ($ticket->user) { // Check if user exists
                $ticket->user->notify(new TicketNotification(
                    $ticket,
                    'ticket_closed',
                    ['message' => "Your ticket #{$ticket->id} has been closed"]
                ));
            } else {
                Log::info("[TicketController@updateStatus] Ticket ID {$ticket->id} closed, but original user (ID: {$ticket->user_id}) is deleted. No notification sent.");
            }
        }

        AuditLogService::log(
            "Updated ticket status",
            "ticket",
            "Updated ticket #{$ticket->id} status to {$validatedData['status']}",
            $ticket->id,
            "Ticket status update"
        );

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function addResponse(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $ticket->user_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'response' => 'required'
        ]);

        $response = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'response' => $validatedData['response'],
            'is_admin_response' => auth()->user()->isAdmin()
        ]);

        // Ja administrators atbild, paziņo biļetes izveidotājam
        if (auth()->user()->isAdmin()) {
            if ($ticket->user) { // Check if user exists
                $ticket->user->notify(new TicketNotification(
                    $ticket,
                    'admin_response',
                    ['message' => "An admin has responded to your ticket #{$ticket->id}"]
                ));
            } else {
                Log::info("[TicketController@addResponse] Admin responded to ticket ID {$ticket->id}, but original user (ID: {$ticket->user_id}) is deleted. No notification sent.");
            }
        }
        // Ja lietotājs atbild, paziņo piešķirtajam administratoram vai visiem administratoriem
        else {
            $notifyUser = $ticket->assigned_admin_id ? User::find($ticket->assigned_admin_id) : null;
            if ($notifyUser) {
                $notifyUser->notify(new TicketNotification(
                    $ticket,
                    'user_response',
                    ['message' => "User has responded to ticket #{$ticket->id}"]
                ));
            } else {
                User::where('usertype', 'admin')->get()->each(function ($admin) use ($ticket) {
                    $admin->notify(new TicketNotification(
                        $ticket,
                        'user_response',
                        ['message' => "User has responded to ticket #{$ticket->id}"]
                    ));
                });
            }
        }

        AuditLogService::log(
            "Added ticket response",
            "ticket",
            "Added response to ticket #{$ticket->id}",
            $ticket->id,
            "Ticket response"
        );

        return back()->with('success', 'Response added successfully.');
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $ticket->update([
            'assigned_admin_id' => auth()->id(),
            'status' => 'in_progress'
        ]);

        // Dzēst jauno biļešu paziņojumus visiem administratoriem
        Log::info("[TicketController@assignTicket] Ticket ID {$ticket->id} assigned to Auth User: " . auth()->id() . ". Attempting to delete 'new_ticket' notifications for all admins.");
        $adminUsers = User::where('usertype', 'admin')->get();
        $adminUsers->each(function ($adminUser) use ($ticket) {
            $deletedCount = $adminUser->notifications()
                ->where('type', 'App\Notifications\TicketNotification')
                ->where('data->ticket_id', $ticket->id)
                ->where('data->type', 'new_ticket')
                ->delete();
            if ($deletedCount > 0) {
                Log::info("[TicketController@assignTicket] Deleted 'new_ticket' notification for admin ID {$adminUser->id} regarding ticket ID {$ticket->id}.");
            }
        });

        AuditLogService::log(
            "Assigned ticket",
            "ticket",
            "Assigned ticket #{$ticket->id} to admin",
            $ticket->id,
            "Ticket assignment"
        );

        return back()->with('success', 'Ticket assigned successfully.');
    }
}
