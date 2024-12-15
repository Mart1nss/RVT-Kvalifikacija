<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
</head>
<body>
    @include('navbar')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2>Support Tickets</h2>
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create New Ticket</a>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        @if(auth()->user()->isAdmin())
                                            <th>User</th>
                                        @endif
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
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
                                                <span class="badge bg-{{ $ticket->status === 'Open' ? 'danger' : ($ticket->status === 'In Progress' ? 'warning' : 'success') }}">
                                                    {{ $ticket->status }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>