<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
  {{-- <link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}"> --}} {{-- Potentially remove if styles are inlined or in user-dashboard.css --}}
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard/user-dashboard.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <title>Dashboard</title>
</head>

@include('components.alert')
@include('navbar')

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
    background-color: #191919;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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


  .user-navigation-links {
    margin-top: 25px;
    padding-top: 15px;
    border-top: 1px solid #333;
  }

  .user-navigation-links .btn {
    background-color: #2a2a2a; 
    color: #fff;
    width: 100%; 
    justify-content: center; 
    border: none;
    padding: 12px 18px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: left;
  }

  .user-navigation-links .btn:hover {
    background-color: #383838;
    transform: translateY(-2px);
  }

  .user-navigation-links .btn i {
    font-size: 1.3em;
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
      padding: 15px;
    }
     #item-continue { 
      height: min-content;
    }
  }

</style>

<div class="main-container">

  <!-- Row 1: Welcome Actions & Continue Reading -->
  <div class="dashboard-row dashboard-row-one">
    <div class="welcome-actions-container"> {{-- Was .item-container --}}
      <h1 class="dashboard-title">Dashboard</h1> {{-- Was .dashboard-text-container-title --}}
      <h2 class="welcome-message"> {{-- Was h1 with inline styles --}}
        Welcome, {{ auth()->user()->name }}!
      </h2>

      <div class="user-navigation-links"> {{-- For user navigation buttons --}}
        <button class="btn" onclick="window.location.href='{{ '/library' }}'">
          <i class='bx bx-library'></i> Library
        </button>
        <button style="margin-top: 10px;" class="btn" onclick="window.location.href='{{ route('forums.index') }}'">
          <i class='bx bxs-chat'></i>Forums
        </button>
        <button style="margin-top: 10px;" class="btn" onclick="window.location.href='{{ route('my-collection') }}'">
          <i class='bx bxs-collection'></i> My Collection
        </button>
        {{-- Add other user-specific buttons here if any, e.g., My Profile, Settings --}}
        {{-- Example:
        <button style="margin-top: 10px;" class="btn" onclick="window.location.href='{{ route('profile.show') }}'">
          <i class='bx bx-user-circle'></i> My Profile
        </button>
        --}}
      </div>
    </div>

    @if (auth()->user()->lastReadBook)
    <div class="continue-reading-wrapper">
        @include('components.continue-reading')
    </div>
    @endif
  </div>

  <!-- Book Carousels -->
  <div class="dashboard-row"> {{-- Carousels in their own row for full width --}}
    <div style="width: 100%; background-color: transparent !important; padding: 0 !important; box-shadow: none !important;">
        @include('components.book-carousels')
    </div>
  </div>
</div>

<script src="{{ asset('js/pdf-carousel.js') }}" defer></script>
