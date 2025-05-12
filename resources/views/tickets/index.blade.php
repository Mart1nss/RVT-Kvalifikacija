<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Support Tickets</title>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="text-container" style="display: flex; justify-content: space-between; align-items: center;">
      <h1 class="text-container-title">Support Tickets</h1>
      @if (!auth()->user()->isAdmin())
        <button onclick="window.location.href = '{{ route('tickets.create') }}'" class="btn btn-primary btn-md">Create
          Ticket</button>
      @endif
    </div>

    <div class="item-container">


      <div class="tabs">
        <a href="{{ route('tickets.index', ['tab' => 'active']) }}"
          class="tab-item {{ request()->get('tab', 'active') === 'active' ? 'active' : '' }}">
          Active
        </a>
        <a href="{{ route('tickets.index', ['tab' => 'closed']) }}"
          class="tab-item {{ request()->get('tab') === 'closed' ? 'active' : '' }}">
          Closed
        </a>
      </div>

      @if (auth()->user()->isAdmin())
        <h2>Tickets</h2>
      @else
        <h2>My Tickets</h2>
      @endif

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th class="mobile-hide">Ticket ID</th>
              @if (auth()->user()->isAdmin())
                <th>User</th>
              @endif
              <th>Title</th>
              <th>Category</th>
              <th class="mobile-hide">Status</th>
              <th class="mobile-hide">{{ request()->get('tab') === 'closed' ? 'Resolved' : 'Created' }}</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($tickets as $ticket)
              <tr>
                <td class="mobile-hide">{{ $ticket->ticket_id }}</td>
                @if (auth()->user()->isAdmin())
                  <td>{{ $ticket->user ? $ticket->user->name : 'Deleted User' }}</td>
                @endif
                <td class="title-cell " title="{{ $ticket->title }}">{{ \Str::limit($ticket->title, 50) }}</td>
                <td>{{ $ticket->category }}</td>
                <td class="mobile-hide ">
                  <span class="status-badge status-{{ $ticket->status }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                  </span>
                </td>
                <td class="mobile-hide">
                  @if (request()->get('tab') === 'closed')
                    {{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y') : 'N/A' }}
                  @else
                    {{ $ticket->created_at->format('M d, Y') }}
                  @endif
                </td>
                <td>
                  <a href="{{ route('tickets.show', $ticket) }}" class="view-btn">View</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
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

    .item-container {
      background-color: #191919;
      border-radius: 8px;
      border-top-left-radius: 0px;
      border-top-right-radius: 0px;
      padding: 0px 16px 16px 16px;
      width: 100%;
      overflow-x: auto;
    }

    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .status-open {
      background-color: rgb(126, 6, 6);
    }

    .status-in_progress {
      background-color: #ffc107;
      color: black;
    }

    .status-closed {
      background-color: rgb(0, 126, 0);
    }

    .custom-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 8px;
      margin-top: 1rem;
    }

    .custom-table th {
      background-color: rgb(13, 13, 13);
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
      background-color: #252525;
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

    .tabs {
      display: flex;
      gap: 0;
      background-color:#191919;
      margin-bottom: 16px;
    }

    .tab-item {
      padding: 12px 24px;
      background-color:#191919;
      color: white;
      text-decoration: none;
      font-family: sans-serif;
      font-weight: 800;
      font-size: 14px;
      text-transform: uppercase;
      transition: all 0.15s;
      border-bottom: 2px solid transparent;
    }

    .tab-item:first-child {
      border-top-left-radius: 8px;
    }

    .tab-item:last-child {
      border-top-right-radius: 8px;
    }

    .tab-item:hover {
      opacity: 0.7;
    }

    .tab-item.active {
      border-bottom: 2px solid white;
    }

    .desktop-hide {
      display: none;
    }

    @media (max-width: 768px) {
      .mobile-hide {
        display: none;
      }

      .custom-table td,
      .custom-table th {
        padding: 10px;
      }

      .title-cell {
        max-width: 150px;
      }

      .item-container {
        padding: 10px;
        border-radius: 8px;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
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

      .tabs {
        margin-top: 12px;
        margin-bottom: 12px;
      }

      .tab-item {
        padding: 8px 16px;
        font-size: 12px;
        flex: 1;
        text-align: center;
      }
    }
  </style>
</body>

</html>
