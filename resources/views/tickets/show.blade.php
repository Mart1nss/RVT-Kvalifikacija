<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    @include('components.alert')
    @include('navbar')

    <div class="main-container">
        <div class="ticket-header">
            <h1>Ticket #{{ $ticket->ticket_id }}</h1>
            <div class="ticket-actions">
                @if(auth()->user()->isAdmin())
                    @if(!$ticket->assigned_admin_id && $ticket->status !== 'resolved')
                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="accept-btn">Accept Ticket</button>
                        </form>
                    @elseif($ticket->assigned_admin_id === auth()->id() && $ticket->status !== 'resolved')
                        <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="status-form">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </form>
                    @else
                        <span class="status-badge status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    @endif
                @else
                    <span class="status-badge status-{{ $ticket->status }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="ticket-info">
            <div class="info-group">
                <label>Title:</label>
                <span>{{ $ticket->title }}</span>
            </div>
            <div class="info-group">
                <label>Category:</label>
                <span>{{ $ticket->category }}</span>
            </div>
            <div class="info-group">
                <label>Created By:</label>
                <span>{{ $ticket->user->name }}</span>
            </div>
            <div class="info-group">
                <label>Assigned To:</label>
                <span>{{ $ticket->assignedAdmin ? $ticket->assignedAdmin->name : 'Unassigned' }}</span>
            </div>
            <div class="info-group">
                <label>Created At:</label>
                <span>{{ $ticket->created_at->format('M d, Y H:i') }}</span>
            </div>
            @if($ticket->resolved_at)
                <div class="info-group">
                    <label>Resolved At:</label>
                    <span>{{ $ticket->resolved_at->format('M d, Y H:i') }}</span>
                </div>
            @endif
        </div>

        <div class="ticket-description">
            <h2>Description</h2>
            <div class="description-content">
                {{ $ticket->description }}
            </div>
        </div>

        <div class="ticket-responses">
            <h2>Responses</h2>
            @foreach($ticket->responses()->orderBy('created_at', 'desc')->get() as $response)
                <div class="response {{ $response->is_admin_response ? 'admin-response' : 'user-response' }}">
                    <div class="response-header">
                        <span class="response-author">{{ $response->user->name }}</span>
                        <span class="response-time">{{ $response->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="response-content">
                        {{ $response->response }}
                    </div>
                </div>
            @endforeach
        </div>

        @if($ticket->status !== 'resolved')
            <div class="response-form">
                <h2>Add Response</h2>
                <form action="{{ route('tickets.respond', $ticket) }}" method="POST">
                    @csrf
                    <textarea name="response" rows="4" required placeholder="Type your response here..."></textarea>
                    <button type="submit" class="submit-btn">Submit Response</button>
                </form>
            </div>
        @endif
    </div>

    <style>
    body {
        background-color: #1c1a1a;
        color: white;
        font-family: sans-serif;
    }

    .main-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .ticket-header h1 {
        color: white;
        text-transform: uppercase;
        font-weight: 800;
        margin: 0;
    }

    .ticket-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .accept-btn, .submit-btn {
        background-color: white;
        color: black;
        padding: 8px 16px;
        border: 1px solid white;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.9rem;
        transition: all 0.15s;
    }

    .accept-btn:hover, .submit-btn:hover {
        opacity: 0.7;
    }

    .status-select {
        background-color: #2d2d2d;
        color: white;
        padding: 8px;
        border: 1px solid #3d3d3d;
        border-radius: 4px;
        cursor: pointer;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-open { background-color: #dc3545; }
    .status-in_progress { background-color: #ffc107; color: black; }
    .status-resolved { background-color: #008000; }

    .ticket-info {
        background-color: #2d2d2d;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .info-group {
        display: flex;
        margin-bottom: 1rem;
        word-break: break-word;
    }

    .info-group label {
        font-weight: bold;
        min-width: 120px;
        color: #aaa;
        flex-shrink: 0;
    }

    .info-group span {
        flex: 1;
        padding-right: 20px;
    }

    .ticket-description {
        margin-bottom: 2rem;
        background-color: #2d2d2d;
        padding: 1.5rem;
        border-radius: 8px;
    }

    .description-content {
        word-break: break-word;
        line-height: 1.6;
        margin-top: 1rem;
    }

    .response {
        background-color: #2d2d2d;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .response-content {
        word-break: break-word;
        line-height: 1.6;
        margin-top: 0.5rem;
    }

    textarea {
        width: 100%;
        background-color: #2d2d2d;
        border: 1px solid #3d3d3d;
        color: white;
        padding: 1rem;
        border-radius: 8px;
        resize: vertical;
        min-height: 100px;
        max-height: 400px;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    textarea:focus {
        outline: none;
        border-color: #4d4d4d;
    }

    .admin-response {
        border-left: 4px solid #008000;
    }

    .user-response {
        border-left: 4px solid white;
    }

    .response-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .response-author {
        font-weight: bold;
    }

    .response-time {
        color: #aaa;
        font-size: 0.9em;
    }

    .response-form textarea {
        width: 100%;
        background-color: #2d2d2d;
        color: white;
        padding: 1rem;
        border: 1px solid #3d3d3d;
        border-radius: 4px;
        margin-bottom: 1rem;
        resize: vertical;
    }

    .response-form textarea:focus {
        outline: none;
        border-color: #4d4d4d;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #4d4d4d;
    }
    </style>
</body>
</html>