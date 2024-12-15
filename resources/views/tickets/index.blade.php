<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
</head>
<body>
    @include('components.alert')
    @include('navbar')

    <div class="main-container">
        <div class="text-container">
            <h1>Support Tickets</h1>
            @if(!auth()->user()->isAdmin())
                <a href="{{ route('tickets.create') }}" class="create-btn">Create New Ticket</a>
            @endif
        </div>

        <div class="item-container">
            <div class="table-responsive">
            @if(auth()->user()->isAdmin())
            <h2>Open Tickets</h2>
            @endif
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            @if(auth()->user()->isAdmin())
                                <th>User</th>
                            @endif
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Assigned</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_id }}</td>
                                @if(auth()->user()->isAdmin())
                                    <td>{{ $ticket->user->name }}</td>
                                @endif
                                <td>{{ $ticket->title }}</td>
                                <td>{{ $ticket->category }}</td>
                                <td>
                                    <span class="status-badge status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->assignedAdmin ? $ticket->assignedAdmin->name : 'Unassigned' }}</td>
                                <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="view-btn">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="resolved-tickets mt-5">
                    <h2>Resolved Tickets</h2>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Resolved By</th>
                                    <th>Resolved At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resolvedTickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_id }}</td>
                                        <td>{{ $ticket->user->name }}</td>
                                        <td>{{ $ticket->title }}</td>
                                        <td>{{ $ticket->category }}</td>
                                        <td>{{ $ticket->resolved_by_user->name ?? 'N/A' }}</td>
                                        <td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="view-btn">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>

    h2 {
        color: white;
        text-transform: uppercase;
        font-family: sans-serif;
        font-weight: 800;
        margin: 0;
    }

    .text-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .text-container h1 {
        color: white;
        text-transform: uppercase;
        font-family: sans-serif;
        font-weight: 800;
        margin: 0;
    }

    .create-btn {
        background-color: white;
        color: black;
        padding: 10px 20px;
        border: 1px solid white;
        border-radius: 8px;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        font-family: sans-serif;
        font-size: 14px;
        text-transform: uppercase;
        transition: all 0.15s;
    }

    .create-btn:hover {
        opacity: 0.7;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: 1rem;
    }

    .custom-table th {
        background-color: #1c1a1a;
        color: white;
        padding: 12px;
        text-align: left;
        font-family: sans-serif;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 12px;
    }

    .custom-table td {
        background-color: #2d2d2d;
        color: white;
        padding: 12px;
        font-family: sans-serif;
    }

    .custom-table tr:hover td {
        background-color: #3d3d3d;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-open {
        background-color: #dc3545;
    }

    .status-in_progress {
        background-color: #ffc107;
        color: black;
    }

    .status-resolved {
        background-color: #28a745;
    }

    .view-btn {
        background-color: white;
        color: black;
        padding: 5px 15px;
        border: 1px solid white;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        transition: all 0.15s;
    }

    .view-btn:hover {
        opacity: 0.7;
    }

    .resolved-tickets h2 {
        color: white;
        font-family: sans-serif;
        font-weight: 800;
        margin: 2rem 0;
        text-transform: uppercase;
    }
    </style>
</body>
</html>