<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/main-style.css') }}">

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<div class="navbar">

  <h1 class="logo"
    style="font-family: sans-serif; color: white; cursor: pointer; font-weight: 800; white-space: nowrap;">ELEVATE READS
  </h1>

  <div class="back-btn-div">
    <span id="back-btn" class='bx bxs-left-arrow-alt '></span>
  </div>

  @auth

    <nav class="nav-btn">
      <button id="notification-btn">
        <i class='bx bx-bell'></i>
        @if (auth()->user()->unreadNotifications->count() > 0)
          <span class="badge">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
      </button>

      <div id="notifications-dropdown" style="display: none;">
        <div class="notifications-header">
          <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800; font-size: 16px;">
            NOTIFICATIONS</h1>
        </div>

        <div class="notifications-content">
          @if (auth()->user()->notifications->isEmpty())
            <p class="no-notifications">No Notifications</p>
          @else
            @foreach (auth()->user()->notifications()->latest()->get() as $notification)
              <div class="notification-item" id="notification-{{ $notification->id }}">
                <div class="notification-content">
                  <div class="notification-text">{{ $notification->data['message'] }}</div>
                  @if (isset($notification->data['link']))
                    <a href="{{ $notification->data['link'] }}" class="goto-link">Go to ticket</a>
                  @endif
                  <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <div class="notification-actions">
                  <button type="button" class="mark-read" onclick="deleteNotification('{{ $notification->id }}')"
                    title="Clear notification">
                    <i class='bx bx-x'></i>
                  </button>
                </div>
              </div>
            @endforeach
          @endif
        </div>

        <div class="notifications-footer">
          <button class="notif-clear-btn" onclick="deleteAllNotifications()"><i style="font-size: 20px;"
              class='bx bx-trash'></i> CLEAR ALL</button>
        </div>
      </div>

      <button class="user-btn" id="dropdown-toggle">
        {{ Auth::user()->name }}
      </button>
      <div class="dropdown-menu" style="display: none;">
        <div class="menu-header">MENU</div>
        <div class="menu-grid">
          <a href="{{ route('home') }}" class="menu-item {{ Request::is('home') ? 'active' : '' }}">
            <i class='bx bx-category-alt'></i>
            <span>DASHBOARD</span>
          </a>
          <a href="{{ route('library') }}" class="menu-item {{ Request::is('library') ? 'active' : '' }}">
            <i class='bx bx-library'></i>
            <span>LIBRARY</span>
          </a>
          <div class="menu-item">
            <i class='bx bx-conversation'></i>
            <span>FORUMS</span>
          </div>
          <div class="menu-item">
            <i class='bx bx-bar-chart-alt-2'></i>
            <span>MY PROGRESS</span>
          </div>
        </div>

        <div class="menu-section {{ Request::is('viewnotes') ? 'active' : '' }}"
          onclick="window.location.href='{{ '/viewnotes' }}'">
          <a href="{{ '/viewnotes' }}" class="section-header">NOTES</a>
        </div>

        <div class="menu-section {{ Request::is('my-collection') ? 'active' : '' }}"
          onclick="window.location.href='{{ '/my-collection' }}'">
          <a href="{{ '/my-collection' }}" class="section-header">MY COLLECTION</a>
        </div>

        <div class="menu-section {{ Request::is('profile') ? 'active' : '' }}"
          onclick="window.location.href='{{ '/profile' }}'">
          <a href="{{ '/profile' }}" class="section-header">SETTINGS</a>
        </div>

        <div class="menu-footer">
          <a href="/tickets" class="support-btn" style="color: black; height: 48px;">Support</a>
          <a class="logout-btn" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
          </a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </div>
    </nav>
  @endauth



</div>

<script>
  // User dropdown toggle
  document.getElementById('dropdown-toggle').addEventListener('click', function(e) {
    e.stopPropagation();
    // Close notifications dropdown if open
    document.querySelector('#notifications-dropdown').style.display = 'none';
    // Toggle user dropdown
    const dropdownMenu = this.nextElementSibling;
    dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
  });

  // Notifications dropdown toggle
  document.getElementById('notification-btn').addEventListener('click', function(e) {
    e.stopPropagation();
    // Close user dropdown if open
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.style.display = 'none');
    // Toggle notifications dropdown
    let dropdown = document.querySelector('#notifications-dropdown');
    if (dropdown.style.display === 'none') {
      dropdown.style.display = 'block';
      // Mark notifications as read when opening dropdown
      fetch('/notifications/mark-read', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(() => {
          const badge = document.querySelector('.badge');
          if (badge) badge.remove();
        });
    } else {
      dropdown.style.display = 'none';
    }
  });

  // Close both dropdowns when clicking outside
  document.addEventListener('click', function(e) {
    if (!e.target.closest('#notification-btn') && !e.target.closest('#notifications-dropdown') &&
      !e.target.closest('#dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
      document.querySelector('#notifications-dropdown').style.display = 'none';
      document.querySelectorAll('.dropdown-menu').forEach(menu => menu.style.display = 'none');
    }
  });

  // Prevent dropdown from closing when clicking inside notifications
  document.querySelector('#notifications-dropdown').addEventListener('click', function(event) {
    event.stopPropagation();
  });

  // Prevent dropdown from closing when clicking inside user menu
  document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  });

  function deleteNotification(notificationId) {
    fetch('/notifications/' + notificationId + '/delete', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(() => {
        const notification = document.getElementById('notification-' + notificationId);
        notification.style.opacity = '0';
        setTimeout(() => {
          notification.remove();
          if (document.querySelectorAll('.notification-item').length === 0) {
            document.querySelector('.notifications-content').innerHTML =
              '<p class="no-notifications">No Notifications</p>';
          }
        }, 300);
      });
  }

  function deleteAllNotifications() {
    fetch('/notifications/delete-all', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach(notification => {
          notification.style.opacity = '0';
        });
        setTimeout(() => {
          document.querySelector('.notifications-content').innerHTML =
            '<p class="no-notifications">No Notifications</p>';
          // Remove the notification badge if it exists
          const badge = document.querySelector('.badge');
          if (badge) badge.remove();
        }, 300);
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }

  function updateNotificationCount() {
    fetch('/notifications/count')
      .then(response => response.json())
      .then(response => {
        const notificationBtn = document.getElementById('notification-btn');
        const existingBadge = document.querySelector('.badge');

        if (response.count > 0) {
          if (existingBadge) {
            existingBadge.textContent = response.count;
          } else {
            const badge = document.createElement('span');
            badge.className = 'badge';
            badge.textContent = response.count;
            notificationBtn.appendChild(badge);
          }
        } else if (existingBadge) {
          existingBadge.remove();
        }
      });
  }

  document.getElementById('back-btn').addEventListener('click', function() {
    window.history.back();
  });
</script>
