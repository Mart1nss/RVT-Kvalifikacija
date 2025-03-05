<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/main-style.css') }}">

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
@livewireStyles

<div class="navbar">

  <h1 class="logo"
    style="font-family: sans-serif; color: white; cursor: pointer; font-weight: 800; white-space: nowrap;">ELEVATE READS
  </h1>

  <div class="back-btn-div">
    <span id="back-btn" class='bx bxs-left-arrow-alt '></span>
  </div>

  @auth

    <nav class="nav-btn">
      <!-- Livewire Notification Dropdown Component -->
      @livewire('notifications.notification-dropdown')

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
          <a href="{{ route('forums.index') }}" class="menu-item {{ Request::is('forums') ? 'active' : '' }}">
            <i class='bx bx-conversation'></i>
            <span>FORUMS</span>
          </a>
          <div class="menu-item">
            <i class='bx bx-bar-chart-alt-2'></i>
            <span>MY PROGRESS<br>
              (neiet)
            </span>
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
  document.addEventListener('DOMContentLoaded', function() {
    // User dropdown toggle
    const dropdownToggle = document.getElementById('dropdown-toggle');
    if (dropdownToggle) {
      dropdownToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        // Close notifications dropdown if open
        if (typeof Livewire !== 'undefined') {
          Livewire.dispatch('closeNotifications');
        }
        // Toggle user dropdown
        const dropdownMenu = this.nextElementSibling;
        dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
      });
    }

    // Close user dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('#dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.style.display = 'none');
      }
    });

    // Prevent dropdown from closing when clicking inside user menu
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
      menu.addEventListener('click', function(event) {
        event.stopPropagation();
      });
    });

    const backBtn = document.getElementById('back-btn');
    if (backBtn) {
      backBtn.addEventListener('click', function() {
        window.history.back();
      });
    }
  });
</script>

@livewireScripts
