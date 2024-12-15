<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
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
      @if($unreadNotifications->isEmpty())
        <p class="no-notifications">No Notifications</p> 
      @else
        @foreach($unreadNotifications as $notification)
          <div class="notification-item">
            <div class="notification-content">
              <div class="notification-text">{{ $notification->message }}</div>
              <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
            <button class="mark-read" data-id="{{ $notification->id }}" onclick="markAsRead(this)" title="Mark as read">
              <i class='bx bx-check'></i>
            </button>
          </div>
        @endforeach
      @endif
    </div>

        <button class="user-btn" id="dropdown-toggle">
            {{ Auth::user()->name }}
        </button>
        <div class="dropdown-menu">
            <a id="dropdown-1" class="dropdown-item" onclick="window.location.href='{{'/home'}}'">{{ __('Dashboard') }}</a> 
            <a id="dropdown-3" class="dropdown-item" onclick="window.location.href='{{'/favorites'}}'">{{ __('Favorites') }}</a> 
            <a id="dropdown-4" class="dropdown-item" onclick="window.location.href='{{'/viewnotes'}}'">{{ __('Notes') }}</a> 
            <a id="dropdown-2" class="dropdown-item" onclick="window.location.href='{{'/profile'}}'">{{ __('Profile') }}</a> 
            <a id="dropdown-5" class="dropdown-item" onclick="window.location.href='{{'/tickets'}}'">{{ __('Support') }}</a>

            <a class="dropdown-item-logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> 
                @csrf 
            </form>
        </div>
      </nav>
  @endauth



</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // User dropdown toggle
  $('#dropdown-toggle').click(function (e) {
    e.stopPropagation();
    // Close notifications dropdown if open
    document.getElementById('notifications-dropdown').style.display = 'none';
    // Toggle user dropdown
    $(this).next('.dropdown-menu').toggle();
  });

  // Notifications dropdown toggle
  document.getElementById('notification-btn').addEventListener('click', function (e) {
    e.stopPropagation();
    // Close user dropdown if open
    $('.dropdown-menu').hide();
    // Toggle notifications dropdown
    let dropdown = document.getElementById('notifications-dropdown');
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
      document.getElementById('notifications-dropdown').style.display = 'none';
      $('.dropdown-menu').hide();
    }
  });

  // Prevent dropdown from closing when clicking inside notifications
  document.getElementById('notifications-dropdown').addEventListener('click', function(event) {
    event.stopPropagation();
  });

  // Prevent dropdown from closing when clicking inside user menu
  $('.dropdown-menu').click(function(event) {
    event.stopPropagation();
  });

  function markAsRead(buttonElement) {
      let notificationId = buttonElement.dataset.id;
      let notificationItem = buttonElement.closest('.notification-item');
      let notificationsDropdown = document.getElementById('notifications-dropdown');

      fetch('/notifications/' + notificationId + '/mark-read', {
          method: 'POST',
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'Content-Type': 'application/json'
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              // Remove the notification item
              notificationItem.remove();

              // Update badge count
              let badge = document.querySelector('.badge');
              if (badge) {
                  let currentCount = parseInt(badge.textContent);
                  let newCount = currentCount - 1;
                  if (newCount === 0) {
                      badge.remove();
                      // Check if there are any notifications left
                      let remainingNotifications = notificationsDropdown.querySelectorAll('.notification-item');
                      if (remainingNotifications.length === 0) {
                          // Show the "no notifications" message
                          notificationsDropdown.innerHTML = '<p class="no-notifications">You have no notifications</p>';
                      }
                  } else {
                      badge.textContent = newCount;
                  }
              }
          }
      })
      .catch(error => {
          console.error('Error:', error);
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
