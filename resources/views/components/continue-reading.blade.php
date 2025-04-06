<link rel="stylesheet" href="{{ asset('css/components/continue-reading.css') }}">

@if (auth()->user()->lastReadBook)
  <div id="item-continue" class="item-container">
    <div class="continue-reading-text-container">
      <h1 class="continue-reading-text-container-title">Last Read</h1>
    </div>

    <div class="continue-reading-content">
      <div class="thumbnail-container" style="">
        <img src="{{ asset('book-thumbnails/' . str_replace('.pdf', '.jpg', auth()->user()->lastReadBook->file)) }}"
          alt="{{ auth()->user()->lastReadBook->title }}" loading="lazy"
          style="width: 100%; height: 100%; object-fit: cover;">
      </div>

      <button onclick="window.location.href='{{ route('view', auth()->user()->lastReadBook->id) }}'"
        class="btn btn-primary btn-full">
        <i class='bx bx-book-reader'></i> Continue
      </button>
    </div>


  </div>
@endif
