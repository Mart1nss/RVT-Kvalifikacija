@include('navbar')

<link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/pdf-carousel.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<script src="{{ asset('js/pdf-carousel.js') }}" defer></script>
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard</title>

<style>
  body {
    background-color: rgb(13, 13, 13);
    color: #fff;
  }

  .main-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding-top: 10px;
  }

  .dashboard-row {
    display: flex;
    gap: 20px;
    width: 100%;
  }

  .dashboard-row > div { 
    background-color: #191919;
    padding: 20px; 
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2); 
  }
  
  .dashboard-row-one > .welcome-actions-container {
    flex: 2.5;
    min-width: 320px;
    padding: 16px;
  }
  .dashboard-row-one > .continue-reading-wrapper {
    flex: 1.5; 
    min-width: 300px; 
    padding: 0px;
  }

  .dashboard-row-two > .top-genres-container {
    flex: 1; 
    min-width: 280px;
  }
  .dashboard-row-two > .stats-section-wrapper { 
    flex: 1; 
    min-width: 320px;
    background-color: transparent !important; 
    padding: 0 !important; 
    box-shadow: none !important;
  }
  

  .welcome-actions-container {
  }

  .dashboard-title {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 10px;
    color: white;
    text-transform: uppercase;
  }

  .welcome-message {
    font-size: 1.5em;
    margin-bottom: 25px;
    color: #ccc;
  }

  .admin-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
    gap: 15px;
    margin-bottom: 20px;
  }

  .admin-actions-grid .btn, .library-action .btn {
    background-color: #fff;
    color: black;
    border: none;
    padding: 12px 18px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    text-align: left;
  }

  .admin-actions-grid .btn:hover, .library-action .btn:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
  }

  .admin-actions-grid .btn i, .library-action .btn i {
    font-size: 1.3em;
  }

  .library-action {
    margin-top: 25px; 
    padding-top: 15px; 
    border-top: 1px solid #333; 
  }

  .library-action .btn {
    background-color: #2a2a2a; 
    color: #fff;
    width: 100%; 
    justify-content: center; 
  }

  .library-action .btn:hover {
    background-color: #383838;
  }

  .top-genres-container h3 {
    font-size: 1.4em;
    font-weight: bold;
    color: #fff;
    margin-bottom: 15px;
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
  }

  .top-genres-container ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .top-genres-container li {
    background-color: #2a2a2a;
    padding: 10px 12px;
    border-radius: 4px;
    margin-bottom: 8px;
    font-size: 0.95em;
    color: #eee;
    display: flex;
    justify-content: space-between;
  }
  
  .top-genres-container li .genre-name {
    font-weight: 500;
  }

  .top-genres-container li .genre-count {
    font-weight: bold;
    color: #fff;
  }

  .top-genres-container .no-data {
    color: #aaa;
    font-style: italic;
  }


  .stats-section {
    display: grid;
    grid-template-columns: repeat(2, 1fr); 
    gap: 20px;
  }

  .stat-card {
    background-color: #191919;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .stat-card i {
    font-size: 2.5em;
    margin-bottom: 12px;
    color: #fff;
  }

  .stat-card .stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #fff;
    margin-bottom: 5px;
  }

  .stat-card .stat-label {
    font-size: 0.95em;
    color: #ccc;
  }

  @media (max-width: 992px) { 
    .dashboard-row {
      flex-direction: column;
    }
    .dashboard-row > div {
      width: 100%; 
      margin-bottom: 20px;
    }
    .dashboard-row > div:last-child {
      margin-bottom: 0;
    }
  }

  @media (max-width: 768px) {
    .welcome-actions-container {
      padding: 8px; 
    }
    .admin-actions-grid {
      grid-template-columns: repeat(2, 1fr); 
      gap: 10px; 
    }
    .stats-section {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
    }
    .stat-card i {
        font-size: 2em;
    }
    .stat-card .stat-number {
        font-size: 1.5em;
    }
  }

  @media (max-width: 768px) {
    #item-continue {
      height: min-content;
    }
  }
</style>

<div class="main-container">

  <!-- Row 1: Welcome Actions & Continue Reading -->
  <div class="dashboard-row dashboard-row-one">
    <div class="welcome-actions-container">
      <h1 class="dashboard-title">Admin Dashboard</h1>
      <h2 class="welcome-message">Welcome, {{ auth()->user()->name }}!</h2>
      <div class="admin-actions-grid">
        <button class="btn" onclick="window.location.href = '{{ route('book-manage') }}'">
          <i class='bx bx-book-alt'></i> Manage Books
        </button>
        <button class="btn" onclick="window.location.href='{{ route('categories.index') }}'">
          <i class='bx bx-category-alt'></i> Categories
        </button>
        <button class="btn" onclick="window.location.href='{{ route('user.management.livewire') }}'">
          <i class='bx bx-user'></i> Manage Users
        </button>
        <button class="btn" onclick="window.location.href='{{ '/notifications' }}'">
          <i class='bx bx-bell'></i> Notifications
        </button>
        <button class="btn" onclick="window.location.href='{{ '/audit-logs' }}'">
          <i class='bx bx-history'></i> Audit Logs
        </button>
      </div>
      <div class="library-action">
        <button class="btn" onclick="window.location.href='{{ '/library' }}'">
          <i class='bx bx-library'></i>Library
        </button>
        <button style="margin-top: 10px;" class="btn" onclick="window.location.href='{{ route('forums.index') }}'">
          <i class='bx bxs-chat'></i>Forums
        </button>
        <button style="margin-top: 10px;" class="btn" onclick="window.location.href='{{ route('my-collection') }}'">
          <i class='bx bxs-collection'></i> My Collection
        </button>
      </div>
    </div>
    <div class="continue-reading-wrapper"> {{-- Wrapper for continue reading --}}
        @include('components.continue-reading')
    </div>
  </div>

  <!-- Row 2: Top Genres & Stats Section -->
  <div class="dashboard-row dashboard-row-two">
    @if(isset($topGenres))
    <div class="top-genres-container">
      <h3><i class='bx bxs-hot'></i> Top Read Genres</h3>
      @if($topGenres->count() > 0)
        <ul>
          @foreach($topGenres as $genre)
            <li>
              <span class="genre-name">{{ $genre->name }}</span>
              <span class="genre-count">{{ $genre->user_read_count }} Readers</span>
            </li>
          @endforeach
        </ul>
      @else
        <p class="no-data">Not enough data to display top genres yet.</p>
      @endif
    </div>
    @else
    <div class="top-genres-container" style="display: none;"></div> {{-- Placeholder to maintain layout if no topGenres --}}
    @endif

    <div class="stats-section-wrapper"> {{-- Wrapper for stats section --}}
        <div class="stats-section">
            <div class="stat-card">
            <i class='bx bxs-book'></i>
            <p class="stat-number">{{ $bookCount }}</p>
            <p class="stat-label">Total Books</p>
            </div>
            <div class="stat-card">
            <i class='bx bxs-user-detail'></i>
            <p class="stat-number">{{ $userCount }}</p>
            <p class="stat-label">Total Users</p>
            </div>
            <div class="stat-card">
            <i class='bx bxs-category'></i>
            <p class="stat-number">{{ $categoryCount }}</p>
            <p class="stat-label">Total Categories</p>
            </div>
            <div class="stat-card">
            <i class='bx bxs-chat'></i> {{-- Icon for forums --}}
            <p class="stat-number">{{ $forumCount }}</p>
            <p class="stat-label">Total Forums</p>
            </div>
        </div>
    </div>
  </div>

  <!-- Row 3: Book Carousels -->
  <div class="dashboard-row">
    <div style="width: 100%; background-color: transparent !important; padding: 0 !important; box-shadow: none !important;">
        @include('components.book-carousels')
    </div>
  </div>

</div>
