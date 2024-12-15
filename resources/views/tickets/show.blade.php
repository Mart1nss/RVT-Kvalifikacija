<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
</head>
<body>
    @include('navbar')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2>Ticket: {{ $ticket->ticket_id }}</h2>
                            @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('tickets.update-status', $ticket) }}" class="d-flex align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </form>
                            @else
                                <span class="badge bg-{{ $ticket->status === 'Open' ? 'danger' : ($ticket->status === 'In Progress' ? 'warning' : 'success') }}">
                                    {{ $ticket->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="ticket-details mb-4">
                            <h5>Title: {{ $ticket->title }}</h5>
                            <p><strong>Category:</strong> {{ $ticket->category }}</p>
                            <p><strong>Created by:</strong> {{ $ticket->user->name }}</p>
                            <p><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y H:i') }}</p>
                            <div class="ticket-description">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $ticket->description }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="responses mb-4">
                            <h4>Responses</h4>
                            @foreach($ticket->responses as $response)
                                <div class="response-item p-3 mb-3 {{ $response->is_admin_response ? 'bg-light' : 'border rounded' }}">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $response->is_admin_response ? 'Admin' : $response->user->name }}</strong>
                                        <small>{{ $response->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <p class="mb-0 mt-2">{{ $response->response }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="add-response">
                            <h4>Add Response</h4>
                            <form method="POST" action="{{ route('tickets.respond', $ticket) }}">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="response" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Response</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>