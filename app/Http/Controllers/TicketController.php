<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Notification;

class TicketController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            // For admins, show unassigned tickets and tickets assigned to them
            $tickets = Ticket::with('user')
                ->where(function($query) {
                    $query->whereNull('assigned_admin_id')
                        ->orWhere('assigned_admin_id', auth()->id());
                })
                ->where('status', '!=', Ticket::STATUS_RESOLVED)
                ->latest()
                ->get();

            // Get resolved tickets for admins
            $resolvedTickets = Ticket::with(['user', 'resolved_by_user'])
                ->where('status', Ticket::STATUS_RESOLVED)
                ->whereNotNull('resolved_at')  // Only get tickets that have actually been resolved
                ->latest('resolved_at')  // Order by resolved_at date
                ->get();

            return view('tickets.index', compact('tickets', 'resolvedTickets'));
        } else {
            // For users, show only their tickets
            $tickets = auth()->user()->tickets()->latest()->get();
            return view('tickets.index', compact('tickets'));
        }
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $ticket = auth()->user()->tickets()->create($request->all());

        // Notify admins about new ticket
        $admins = User::where('usertype', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => "New support ticket ({$ticket->ticket_id}) created by {$ticket->user->name}",
                'link' => route('tickets.show', $ticket)
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        return view('tickets.show', compact('ticket'));
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        // Check if ticket is already assigned or resolved
        if ($ticket->assigned_admin_id || $ticket->status === Ticket::STATUS_RESOLVED) {
            return back()->with('error', 'This ticket cannot be assigned.');
        }

        // Assign ticket to current admin and set status to in_progress
        $ticket->assigned_admin_id = auth()->id();
        $ticket->status = Ticket::STATUS_IN_PROGRESS;
        $ticket->save();

        // Notify ticket owner
        Notification::create([
            'user_id' => $ticket->user_id,
            'message' => "Your ticket ({$ticket->ticket_id}) has been accepted by " . auth()->user()->name,
            'link' => route('tickets.show', $ticket)
        ]);

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Don't allow status updates for resolved tickets
        if ($ticket->status === Ticket::STATUS_RESOLVED) {
            return back()->with('error', 'Cannot update status of resolved tickets.');
        }

        $request->validate([
            'status' => ['required', 'in:' . Ticket::STATUS_IN_PROGRESS . ',' . Ticket::STATUS_RESOLVED]
        ]);

        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        
        // If status is being set to resolved
        if ($request->status === Ticket::STATUS_RESOLVED && $oldStatus !== Ticket::STATUS_RESOLVED) {
            $ticket->resolved_at = now();
            $ticket->resolved_by = auth()->id();
            
            // Notify ticket owner about resolution
            Notification::create([
                'user_id' => $ticket->user_id,
                'message' => "Your ticket ({$ticket->ticket_id}) has been resolved by " . auth()->user()->name,
                'link' => route('tickets.show', $ticket)
            ]);
        }

        $ticket->save();

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function addResponse(Request $request, Ticket $ticket)
    {
        // Check if ticket is resolved
        if ($ticket->status === Ticket::STATUS_RESOLVED) {
            return back()->with('error', 'Cannot respond to resolved tickets.');
        }

        $request->validate([
            'response' => 'required|string'
        ]);

        $ticket->responses()->create([
            'user_id' => auth()->id(),
            'response' => $request->response,
            'is_admin_response' => auth()->user()->isAdmin()
        ]);

        // If this is an admin response, notify the ticket owner
        if (auth()->user()->isAdmin()) {
            Notification::create([
                'user_id' => $ticket->user_id,
                'message' => "New admin response on your ticket ({$ticket->ticket_id})",
                'link' => route('tickets.show', $ticket)
            ]);
        } else {
            // If this is a user response, notify assigned admin if exists
            if ($ticket->assigned_admin_id) {
                Notification::create([
                    'user_id' => $ticket->assigned_admin_id,
                    'message' => "New user response on ticket {$ticket->ticket_id}",
                    'link' => route('tickets.show', $ticket)
                ]);
            }
        }

        return back()->with('success', 'Response added successfully.');
    }
}
