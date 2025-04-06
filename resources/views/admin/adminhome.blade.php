@include('navbar')

<link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/pdf-carousel.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
<script src="{{ asset('js/pdf-carousel.js') }}" defer></script>
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard</title>

<style>
  .btn-div button {
    text-align: left;
  }

  @media (max-width: 768px) {
    #item-continue {
      height: min-content;
    }
  }
</style>

<div class="main-container">

  <div class="big-item-container">

    <div class="item-container">

      <div class="dashboard-text-container">
        <h1 class="dashboard-text-container-title">Admin Dashboard</h1>
      </div>

      <h1 class="welcome-message">
        Welcome, {{ auth()->user()->name }}!</h1>

      <div class="admin-btn-div">

        <button style="margin-bottom: 15px;" class="btn btn-primary btn-right btn-md"
          onclick="window.location.href = '{{ route('book-manage') }}'"><i id="dashboardIcon" class='bx bx-cog'></i>
          Manage
          Books</button>

        <button style="margin-bottom: 15px;" class="btn btn-primary btn-md"
          onclick="window.location.href='{{ route('categories.index') }}'"><i id="dashboardIcon"
            class='bx bx-category'></i>
          Categories</button>

        <button style="margin-bottom: 15px;" class="btn btn-primary btn-md"
          onclick="window.location.href='{{ route('user.management.livewire') }}'"><i id="dashboardIcon"
            class='bx bx-user'></i>
          Manage Users</button>

        <button style="margin-bottom: 15px;" class="btn btn-primary btn-md"
          onclick="window.location.href='{{ '/notifications' }}'"><i id="dashboardIcon" class='bx bx-bell'></i>
          Notifications</button>

        <button style="margin-bottom: 15px;" class="btn btn-primary btn-md"
          onclick="window.location.href='{{ '/audit-logs' }}'"><i id="dashboardIcon" class='bx bx-history'></i>
          Audit Logs</button>

      </div>

      <div class="btn-div">
        <button style="margin-bottom: 15px;" class="btn btn-primary btn-md"
          onclick="window.location.href='{{ '/library' }}'"><i id="dashboardIcon" class='bx bx-book'></i>
          Library</button>
      </div>

    </div>

    @include('components.continue-reading')

  </div>

  <div class="stats-div">

    <div class="stats-card">
      <p class="stats-text">Total Books: {{ $bookCount }}</p>
      <p class="stats-text">Total Users: {{ $userCount }}</p>
    </div>

  </div>

  @include('components.book-carousels')

</div>
