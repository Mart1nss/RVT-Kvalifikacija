<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Notification;
use App\Notifications\NewTicketNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use App\Notifications\NewTicketResponseNotification;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->isAdmin() 
            ? Ticket::with('user')->latest()->get()
            : auth()->user()->tickets()->latest()->get();
        
        return view('tickets.index', compact('tickets'));
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

        // Notify admins
        $admins = User::where('usertype', 'admin')->get();
        Notification::send($admins, new NewTicketNotification($ticket));

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        return view('tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        
        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved'
        ]);

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $request->status]);

        if ($oldStatus !== $request->status) {
            // Notify ticket owner
            $ticket->user->notify(new TicketStatusUpdatedNotification($ticket));
        }

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function addResponse(Request $request, Ticket $ticket)
    {
        $this->authorize('respond', $ticket);
        
        $request->validate([
            'response' => 'required|string'
        ]);

        $response = $ticket->responses()->create([
            'user_id' => auth()->id(),
            'response' => $request->response,
            'is_admin_response' => auth()->user()->isAdmin()
        ]);

        if (auth()->user()->isAdmin()) {
            // Notify ticket owner
            $ticket->user->notify(new NewTicketResponseNotification($ticket, $response));
        } else {
            // Notify admins
            $admins = User::where('usertype', 'admin')->get();
            Notification::send($admins, new NewTicketResponseNotification($ticket, $response));
        }

        return back()->with('success', 'Response added successfully.');
    }
}
