<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
</head>

<body>

  @include('navbar')

  <!-- Floating Alert -->
  <div class="alert-container" id="alertContainer">
    <div class="alert alert-success">
      Notification sent successfully!
    </div>
  </div>


  <div class="main-container">
    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Send
        Notification</h1>
    </div>

    <div class="item-container">
      <div class="filter-div">
        @if (session('success'))
          <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('admin.send.notification') }}" class="notification-form"
          id="notificationForm">
          @csrf
          <input type="text" class="notif-input" name="message" placeholder="Enter your notification message"
            required>
          <button class="send-btn" type="submit">Send</button>
        </form>
      </div>
    </div>

    <div class="notifications-list">
      @if ($notifications = \App\Models\SentNotification::latest()->get())
        @if ($notifications->isEmpty())
          <div class="empty-state">
            <p>No notifications have been sent yet.</p>
          </div>
        @else
          @foreach ($notifications as $notification)
            <div class="notification-item" id="notification-{{ $notification->id }}">
              <div class="notification-content">
                <div class="notification-text">{{ $notification->message }}</div>
                <div class="notification-info">
                  <span class="notification-time">
                    Sent by {{ $notification->sender->name }} {{ $notification->created_at->diffForHumans() }}
                  </span>
                  <span class="read-count">
                    {{ $notification->read_count }}/{{ $notification->total_users }} users read
                  </span>
                </div>
              </div>
              <div class="notification-actions">
                <button type="button" class="delete-btn" onclick="deleteSentNotification('{{ $notification->id }}')"
                  title="Delete notification">
                  <i class='bx bx-trash'></i>
                </button>
              </div>
            </div>
          @endforeach
        @endif
      @endif
    </div>
  </div>

  <script>
    document.getElementById('notificationForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = this;
      const alertContainer = document.getElementById('alertContainer');
      const submitButton = form.querySelector('button[type="submit"]');

      // Disable submit button and show loading state
      submitButton.disabled = true;
      submitButton.innerHTML = 'Sending...';

      fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          alertContainer.style.display = 'block';
          form.reset();

          setTimeout(() => {
            alertContainer.style.display = 'none';
            // Reload the page to show the new notification
            window.location.reload();
          }, 1000);
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Failed to send notification. Please try again.');
        })
        .finally(() => {
          // Re-enable submit button and restore original text
          submitButton.disabled = false;
          submitButton.innerHTML = 'Send';
        });
    });

    function deleteSentNotification(id) {
      if (!confirm(
          'Are you sure you want to delete this notification? This will remove it from all users\' notifications.')) {
        return;
      }

      const notificationElement = document.getElementById(`notification-${id}`);
      notificationElement.style.opacity = '0.5';

      fetch(`/notifications/sent/${id}/delete`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            notificationElement.remove();
            // Check if there are no more notifications
            if (document.querySelectorAll('.notification-item').length === 0) {
              document.querySelector('.notifications-list').innerHTML =
                '<div class="empty-state"><p>No notifications have been sent yet.</p></div>';
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Failed to delete notification');
          notificationElement.style.opacity = '1';
        });
    }
  </script>

  <style>
    .notification-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 0.5rem;
      color: #aaa;
      font-size: 0.9em;
    }

    .read-count {
      background-color: #2d2d2d;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.8em;
    }

    .delete-btn {
      background: none;
      border: none;
      color: #dc3545;
      cursor: pointer;
      padding: 5px;
      font-size: 1.2em;
      transition: opacity 0.15s;
    }

    .delete-btn:hover {
      opacity: 0.7;
    }

    .empty-state {
      text-align: center;
      padding: 2rem;
      color: #aaa;
    }

    .notification-item {
      transition: opacity 0.3s;
    }

    button:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
  </style>

</body>

</html>
