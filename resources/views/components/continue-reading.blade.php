<link rel="stylesheet" href="{{ asset('css/components/continue-reading.css') }}">

@if (auth()->user()->lastReadBook)
  <div id="item-continue" class="item-container">
    <div class="continue-reading-text-container">
      <h1 class="continue-reading-text-container-title">Last Read</h1>
    </div>

    <div class="continue-reading-content"> 
      <div class="book-display-area"> 
        <div class="thumbnail-container">
          @if(auth()->user()->lastReadBook->file)
            <img src="{{ asset('book-thumbnails/' . str_replace('.pdf', '.jpg', auth()->user()->lastReadBook->file)) }}"
                 alt="{{ auth()->user()->lastReadBook->title }}" loading="lazy">
          @else
            <div class="placeholder-thumbnail">No Image</div>
          @endif
        </div>

        <div class="book-details-container">
          <h4>{{ auth()->user()->lastReadBook->title }}</h4>
          <p class="author">{{ auth()->user()->lastReadBook->author }}</p>
          @if(auth()->user()->lastReadBook->category)
            <p class="category">{{ auth()->user()->lastReadBook->category->name }}</p>
          @endif
          <div class="rating">
            <i class='bx bxs-star'></i>
            <span>{{ number_format(auth()->user()->lastReadBook->average_rating ?? 0, 1) }}/5</span>
          </div>
        </div>
      </div>

      <button onclick="window.location.href='{{ route('view', auth()->user()->lastReadBook->id) }}'"
        class="btn btn-primary btn-full btn-continue-reading">
        <i class='bx bx-book-reader'></i> Continue Reading
      </button>
    </div>
  </div>
@endif
