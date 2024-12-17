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
                            <th class="mobile-hide">Ticket ID</th>
                            @if(auth()->user()->isAdmin())
                                <th>User</th>
                            @endif
                            <th>Title</th>
                            <th class="mobile-hide">Category</th>
                            <th>Status</th>
                            <th class="mobile-hide">Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="mobile-hide">{{ $ticket->ticket_id }}</td>
                                @if(auth()->user()->isAdmin())
                                    <td>{{ $ticket->user->name }}</td>
                                @endif
                                <td class="title-cell" title="{{ $ticket->title }}">{{ \Str::limit($ticket->title, 50) }}</td>
                                <td class="mobile-hide">{{ $ticket->category }}</td>
                                <td>
                                    <span class="status-badge status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="mobile-hide">{{ $ticket->created_at->format('M d, Y') }}</td>
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
                                    <th class="mobile-hide">Ticket ID</th>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th class="mobile-hide">Category</th>
                                    <th class="mobile-hide">Resolved At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resolvedTickets as $ticket)
                                    <tr>
                                        <td class="mobile-hide">{{ $ticket->ticket_id }}</td>
                                        <td>{{ $ticket->user->name }}</td>
                                        <td class="title-cell" title="{{ $ticket->title }}">{{ \Str::limit($ticket->title, 50) }}</td>
                                        <td class="mobile-hide">{{ $ticket->category }}</td>
                                        <td class="mobile-hide">{{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y') : 'N/A' }}</td>
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

    .item-container {
        background-color: rgb(37, 37, 37);
        border-radius: 8px;
        padding: 20px;
        width: 100%;
        overflow-x: auto;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
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
        padding: 12px 16px;
        text-align: left;
        font-family: sans-serif;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 12px;
        white-space: nowrap;
    }

    .custom-table td {
        background-color: #2d2d2d;
        color: white;
        padding: 12px 16px;
        font-family: sans-serif;
    }

    .title-cell {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        white-space: nowrap;
    }

    .view-btn {
        background-color: white;
        color: black;
        padding: 5px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: 12px;
        text-transform: uppercase;
        transition: all 0.15s;
        white-space: nowrap;
        display: inline-block;
    }

    .view-btn:hover {
        opacity: 0.7;
    }

    @media (max-width: 768px) {
        .mobile-hide {
            display: none;
        }

        .custom-table td, .custom-table th {
            padding: 10px;
        }

        .title-cell {
            max-width: 150px;
        }

        .item-container {
            padding: 10px;
            border-radius: 8px;
        }

        .text-container {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .create-btn {
            width: 100%;
            text-align: center;
        }
    }
    </style>
</body>
</html>