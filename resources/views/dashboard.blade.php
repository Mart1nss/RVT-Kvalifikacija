<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
  <link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pdf-carousel.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard/user-dashboard.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css">
  <title>Dashboard</title>
</head>

@include('components.alert')
@include('navbar')

<style>
  .logo {
    display: flex;
  }
</style>



<div class="main-container">

  <div class="big-item-container">

    <div class="item-container">
      <div class="dashboard-text-container">
        <h1 class="dashboard-text-container-title">Dashboard</h1>
      </div>
      <h1 style="margin-bottom: 20px; color: white; font-family: sans-serif; font-weight: 800; font-size: 18px;">
        Welcome,
        {{ auth()->user()->name }}!</h1>

      <button style="margin-bottom: 15px;" class="btn btn-primary btn-md"
        onclick="window.location.href='{{ '/library' }}'"><i id="dashboardIcon" class='bx bx-book'></i>
        Library</button>
    </div>

    @if (auth()->user()->lastReadBook)
      <div id="item-continue" class="item-container">
        <div class="dashboard-text-container">
          <h1 class="dashboard-text-container-title">Continue Reading</h1>
        </div>

        <div class="continue-reading-content">
          <div class="thumbnail-container"
            style="display: flex; align-items: center; justify-content: center; width: 250px; height: 370px;">
            <div class="thumbnail" data-pdfpath="/assets/{{ auth()->user()->lastReadBook->file }}"
              style="width: 100%; height: 100%; object-fit: cover;">
              <div class="loading-indicator">
                <i class='bx bx-loader-alt'></i>
              </div>
            </div>
          </div>
          <div>
            <h3 style="color: white; font-size: 14px; margin-bottom: 5px;">{{ auth()->user()->lastReadBook->title }}
            </h3>
            <p style="color: #999; font-size: 12px; margin-bottom: 10px;">{{ auth()->user()->lastReadBook->author }}
            </p>
            <button onclick="window.location.href='{{ route('view', auth()->user()->lastReadBook->id) }}'"
              class="btn btn-primary btn-sm">
              <i class='bx bx-book-reader'></i> Continue Reading
            </button>
          </div>
        </div>


      </div>
    @endif

  </div>

  @include('components.book-carousels')
</div>

<script src="{{ asset('js/pdf-carousel.js') }}" defer></script>
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
