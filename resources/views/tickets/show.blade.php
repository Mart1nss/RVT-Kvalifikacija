<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Details</title>
  <link rel="stylesheet" href="{{ asset('css/alert.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="ticket-header">
      <h1>Ticket #{{ $ticket->id }}</h1>
      <div class="ticket-actions">
        @if (auth()->user()->isAdmin())
          @if (!$ticket->assigned_admin_id && $ticket->status !== 'closed')
            <form action="{{ route('tickets.assign', $ticket) }}" method="POST" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-primary btn-md">Accept Ticket</button>
            </form>
          @elseif($ticket->assigned_admin_id === auth()->id() && $ticket->status !== 'closed')
            <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="status-form">
              @csrf
              @method('PATCH')
              <select name="status" class="status-select" onchange="this.form.submit()">
                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress
                </option>
                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
              </select>
            </form>
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
        <span>{{ $ticket->user ? $ticket->user->name : 'Deleted User' }}</span>
      </div>
      <div class="info-group">
        <label>Assigned To:</label>
        <span>
          @if ($ticket->assignedAdmin)
            {{ $ticket->assignedAdmin->name }}
          @elseif (is_null($ticket->assigned_admin_id))
            <span style="color: red;">unassigned</span>
          @else 
            {{-- This case implies assigned_admin_id has a value, but the user model is gone, or our new logic hasn't run on this old ticket yet --}}
            <span style="color: orange;">Assigned to deleted/unknown admin (ID: {{ $ticket->assigned_admin_id }})</span>
          @endif
        </span>
      </div>
      <div class="info-group">
        <label>Created At:</label>
        <span>{{ $ticket->created_at->format('M d, Y H:i') }}</span>
      </div>
      @if ($ticket->resolved_at)
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
      <div class="responses-list" id="responses-list">
        @foreach ($ticket->responses()->orderBy('created_at', 'asc')->get() as $response)
          <div class="response {{ $response->is_admin_response ? 'admin-response' : 'user-response' }}">
            <div class="response-header">
              <span class="response-author">{{ $response->user ? $response->user->name : ($response->is_admin_response ? 'Deleted Admin' : 'Deleted User') }}</span>
              <span class="response-time">{{ $response->created_at->format('M d, Y H:i') }}</span>
            </div>
            <div class="response-content">
              {{ $response->response }}
            </div>
          </div>
        @endforeach
      </div>
    </div>

    @if ($ticket->status !== 'closed')
      @php
        $hasAdminResponse = $ticket->responses()->where('is_admin_response', true)->exists();
        $isAssignedAdmin = auth()->user()->isAdmin() && $ticket->assigned_admin_id === auth()->id();
        $canUserRespond = !auth()->user()->isAdmin() && $ticket->status === 'in_progress' && $hasAdminResponse;
        $canAdminRespond = $isAssignedAdmin && $ticket->status === 'in_progress';
      @endphp

      @if ($canUserRespond || $canAdminRespond)
        <div class="response-form">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <h2>Add Response</h2>
            <span class="char-counter" id="ticketResponseCounter" style="font-size: 14px; color: #aaa;">0/1000</span>
          </div>
          <form action="{{ route('tickets.respond', $ticket) }}" method="POST">
            @csrf
            <div class="input-wrapper">
              <textarea name="response" id="ticketResponseTextarea" rows="4" required placeholder="Type your response here..." maxlength="1000" data-counter="ticketResponseCounter"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-md" style="margin-top: 10px;">Submit Response</button>
          </form>
        </div>
      @elseif (!auth()->user()->isAdmin())
        <div class="waiting-message">
          <p>Please wait for an admin to accept and respond to your ticket before adding a response.</p>
        </div>
      @elseif (auth()->user()->isAdmin() && $ticket->status === 'open')
        <div class="waiting-message">
          <p>Please accept the ticket first before adding a response.</p>
        </div>
      @elseif (auth()->user()->isAdmin() && $ticket->assigned_admin_id !== auth()->id())
        <div class="waiting-message">
          <p>This ticket is assigned to another admin.</p>
        </div>
      @endif
    @endif
  </div>

  <style>
    .main-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 0 10px;
      color: white;
    }

    .ticket-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }

    .ticket-header h1 {
      color: white;
      font-size: 32px;
      text-transform: uppercase;
      font-weight: 800;
      margin: 0;
    }

    .ticket-actions {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .status-select {
      background-color: #191919;
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

    .ticket-info {
      background-color: #191919;
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
      background-color: #191919;
      padding: 1.5rem;
      border-radius: 8px;
    }

    .description-content {
      word-break: break-word;
      line-height: 1.6;
      margin-top: 1rem;
    }

    .ticket-responses {
      background-color: #191919;
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 1rem;
    }

    .responses-list {
      display: flex;
      margin-top: 10px;
      flex-direction: column;
      max-height: 400px;
      overflow-y: auto;
    }

    .response {
      background-color: #252525;
      border: none;
      padding: 1.5rem;
      border-radius: 8px;
      margin: 6px 0px;

    }

    .response-content {
      word-break: break-word;
      line-height: 1.6;
      margin-top: 0.5rem;
    }

    textarea {
      width: 100%;
      background-color: #191919;
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

    .response-form {
      margin-bottom: 20px;
    }

    .response-form textarea {
      width: 100%;
      margin-top: 10px;
      background-color: #191919;
      color: white;
      padding: 1rem;
      border: none;
      border-radius: 8px;
      margin-bottom: 1rem;
      resize: vertical;
    }

    .response-form textarea:focus {
      outline: white 1px solid;
      background-color: rgb(36, 36, 36);
      border-color: white;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #4d4d4d;
    }

    .waiting-message {
      background-color: #191919;
      padding: 1rem;
      border-radius: 8px;
      margin-top: 1rem;
      text-align: center;
      border: 1px solid #333;
    }

    .waiting-message p {
      color: #aaa;
      margin: 0;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .ticket-header h1 {
        font-size: 28px;
      }

      .ticket-info {
        padding: 1rem;
      }

      .ticket-description {
        padding: 1rem;
      }

      .ticket-responses {
        padding: 1rem;
      }

    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Scroll to bottom of responses
      const responsesList = document.getElementById('responses-list');
      if (responsesList) {
        responsesList.scrollTop = responsesList.scrollHeight;
      }

      // Character counter for ticket response
      const ticketTextarea = document.getElementById('ticketResponseTextarea');
          const ticketCounter = document.getElementById('ticketResponseCounter');
      
      if (ticketTextarea && ticketCounter) {
        const maxLength = parseInt(ticketTextarea.getAttribute('maxlength') || "1000", 10);
        
        const updateCount = () => {
          const count = ticketTextarea.value.length;
          ticketCounter.textContent = `${count}/${maxLength}`;
          if (count > maxLength) {
            ticketCounter.style.color = 'red';
            // Optionally disable submit button if over limit
            // document.querySelector('.response-form button[type="submit"]').disabled = true;
          } else {
            ticketCounter.style.color = '#aaa';
            // document.querySelector('.response-form button[type="submit"]').disabled = false;
          }
        };
        
        ticketTextarea.addEventListener('input', updateCount);
        updateCount(); // Initial count
      }
    });
  </script>
</body>

</html>
