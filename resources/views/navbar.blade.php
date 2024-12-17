<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<div class="navbar">

  <h1 class="logo" style="font-family: sans-serif; color: white; cursor: pointer; font-weight: 800; white-space: nowrap;">ELEVATE READS</h1>

  <div class="back-btn-div">
    <span id="back-btn" class='bx bxs-left-arrow-alt'></span>
  </div>

  @auth 

    <nav class="nav-btn">
      <button id="notification-btn">
        <i class='bx bx-bell'></i>
        @if($unreadNotifications->count())
            <span class="badge">{{ $unreadNotifications->count() }}</span>
        @endif
    </button>

    <div id="notifications-dropdown" style="display: none;">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800; font-size: 16px; padding: 10px;">NOTIFICATIONS</h1>

      
      @if($unreadNotifications->isEmpty())
        <p class="no-notifications">No Notifications</p> 
      @else
        @foreach($unreadNotifications as $notification)
          <div class="notification-item" id="notification-{{ $notification->id }}">
            <div class="notification-content">
              <div class="notification-text">{{ $notification->message }}</div>
              @if($notification->link)
                <a href="{{ $notification->link }}" class="goto-link">Go to</a>
              @endif
              <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
            <div class="notification-actions">
              <button type="button" class="mark-read" onclick="markAsRead({{ $notification->id }})" title="Mark as read">
                <i class='bx bx-check'></i>
              </button>
            </div>
          </div>
        @endforeach
      @endif

      <button class="notif-clear-btn" onclick="clearAllNotifications()"><i style="font-size: 20px;" class='bx bx-x'></i> CLEAR ALL</button>
    </div>

        <button class="user-btn" id="dropdown-toggle">
            {{ Auth::user()->name }}
        </button>
        <div class="dropdown-menu">
            <div class="menu-header">MENU</div>
            <div class="menu-grid">
                <a href="{{ route('home') }}" class="menu-item {{ Request::is('home') ? 'active' : '' }}">
                    <i class='bx bxs-dashboard'></i>
                    <span>DASHBOARD</span>
                </a>
                <a href="{{ route('bookpage') }}" class="menu-item {{ Request::is('bookpage') ? 'active' : '' }}">
                    <i class='bx bx-book'></i>
                    <span>LIBRARY</span>
                </a>
                <div class="menu-item">
                  <i class='bx bx-conversation'></i>
                    <span>FORUMS</span>
                </div>
                <div class="menu-item">
                    <i class='bx bx-ghost'></i>
                    <span>Placeholder 4</span>
                </div>
            </div>
            
            <div class="menu-section {{ Request::is('viewnotes') ? 'active' : '' }}" onclick="window.location.href='{{'/viewnotes'}}'">
                <a href="{{'/viewnotes'}}" class="section-header">NOTES</a>
            </div>
            
            <div class="menu-section {{ Request::is('favorites') ? 'active' : '' }}" onclick="window.location.href='{{'/favorites'}}'">
                <a href="{{'/favorites'}}" class="section-header">FAVORITES</a>
            </div>
            
            <div class="menu-section {{ Request::is('profile') ? 'active' : '' }}" onclick="window.location.href='{{'/profile'}}'">
                <a href="{{'/profile'}}" class="section-header">SETTINGS</a>
            </div>
            
            <div class="menu-footer">
                <a href="/tickets" class="support-btn" style="color: black;">Support</a>
                <a class="logout-btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

<style>
.notification-dropdown {
  padding: 1rem;
  max-height: 400px;
  overflow-y: auto;
}

.notification-header {
  margin-bottom: 1rem;
  font-size: 1rem;
  color: #333;
}

.no-notifications {
  color: #666;
  text-align: center;
  padding: 1rem;
}

.notification-item {
  padding: 0.75rem;
  border-bottom: 1px solid #eee;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.notification-time {
  color: #666;
  font-size: 0.8rem;
}

.notification-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-top: 0.5rem;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // User dropdown toggle
  $('#dropdown-toggle').click(function (e) {
    e.stopPropagation();
    // Close notifications dropdown if open
    document.querySelector('#notifications-dropdown').style.display = 'none';
    // Toggle user dropdown
    $(this).next('.dropdown-menu').toggle();
  });

  // Notifications dropdown toggle
  document.getElementById('notification-btn').addEventListener('click', function (e) {
    e.stopPropagation();
    // Close user dropdown if open
    $('.dropdown-menu').hide();
    // Toggle notifications dropdown
    let dropdown = document.querySelector('#notifications-dropdown');
    if (dropdown.style.display === 'none') {
      dropdown.style.display = 'block';
    } else {
      dropdown.style.display = 'none';
    }
  });

  // Close both dropdowns when clicking outside
  document.addEventListener('click', function(e) {
    if (!e.target.closest('#notification-btn') && !e.target.closest('#notifications-dropdown') && 
        !e.target.closest('#dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
      document.querySelector('#notifications-dropdown').style.display = 'none';
      $('.dropdown-menu').hide();
    }
  });

  // Prevent dropdown from closing when clicking inside notifications
  document.querySelector('#notifications-dropdown').addEventListener('click', function(event) {
    event.stopPropagation();
  });

  // Prevent dropdown from closing when clicking inside user menu
  $('.dropdown-menu').click(function(event) {
    event.stopPropagation();
  });

  function markAsRead(notificationId) {
    $.ajax({
        url: '/notifications/' + notificationId + '/mark-read',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#notification-' + notificationId).fadeOut();
            updateNotificationCount();
        }
    });
}

function clearAllNotifications() {
    $.ajax({
        url: '/notifications/mark-all-read',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('.notification-item').fadeOut();
            updateNotificationCount();
        }
    });
}

function updateNotificationCount() {
    $.get('/notifications/count', function(response) {
        if (response.count > 0) {
            $('.badge').text(response.count);
        } else {
            $('.badge').remove();
            $('#notifications-dropdown').html('<p class="no-notifications">No Notifications</p>');
        }
    });
}

  document.getElementById('back-btn').addEventListener('click', function() {
      const previousURL = localStorage.getItem('previousURL');
      const currentURL = window.location.href;

      if (previousURL && previousURL !== currentURL) {
          window.location.href = previousURL;
      } else {
          window.location.href = '{{ route('home') }}';
      }
  });

  // Get the current and previous URL from localStorage
  const currentURL = window.location.href;
  const lastVisitedURL = localStorage.getItem('currentURL');

  // Only update previousURL if the current URL is different from the last visited URL
  if (lastVisitedURL && lastVisitedURL !== currentURL) {
      localStorage.setItem('previousURL', lastVisitedURL);
  }

  // Update currentURL to the current URL
  localStorage.setItem('currentURL', currentURL);
</script>
