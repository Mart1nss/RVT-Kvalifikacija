<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Library</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
  <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css"
    integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="text-container">
      <h1 class="text-container-title">Library</h1>
      <div class="search-filter-container">
        <div class="search-container">
          <input type="text" id="search-input" placeholder="Search books...">
        </div>
        <button class="mobile-filter-btn">
          <i class='bx bx-filter-alt'></i>
        </button>
        <div class="genre-dropdown">
          <button class="dropdown-btn"><i class='bx bx-filter-alt'></i> Genres</button>
          <ul class="dropdown-content">
            <!-- Genres will be populated by JavaScript -->
          </ul>
        </div>

        <div class="sort-dropdown">
          <button class="dropdown-btn"><i class='bx bx-sort-alt-2'></i> Newest</button>
          <ul class="dropdown-content">
            <li data-value="newest">Newest</li>
            <li data-value="oldest">Oldest</li>
            <li data-value="title_asc">Title (A-Z)</li>
            <li data-value="title_desc">Title (Z-A)</li>
            <li data-value="author_asc">Author (A-Z)</li>
            <li data-value="author_desc">Author (Z-A)</li>
            <li data-value="rating_asc">Rating Asc </li>
            <li data-value="rating_desc">Rating Desc</li>
          </ul>
          <select id="sortSelect" style="display: none;">
            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest First</option>
            <option value="title_asc" {{ $sort == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
            <option value="title_desc" {{ $sort == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
            <option value="author_asc" {{ $sort == 'author_asc' ? 'selected' : '' }}>Author (A-Z)</option>
            <option value="author_desc" {{ $sort == 'author_desc' ? 'selected' : '' }}>Author (Z-A)</option>
            <option value="rating_asc" {{ $sort == 'rating_asc' ? 'selected' : '' }}>Rating (Low-High)</option>
            <option value="rating_desc" {{ $sort == 'rating_desc' ? 'selected' : '' }}>Rating (High-Low)</option>
          </select>
        </div>

      </div>

      <div class="filter-info-row" style="display: none;">
        <span class="total-count"><span id="book-count">0</span> books</span>
        <div id="active-filters">
          <!-- Active genre filters will be added here dynamically -->
        </div>
        <button class="clear-filters-btn" style="display: none;">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      </div>
    </div>

    <div class="item-container">
      @foreach ($data as $book)
        <div class="pdf-item" data-book-id="{{ $book->id }}" data-genre="{{ $book->category->name ?? '' }}">
          <div class="rating-badge">
            <i class='bx bxs-star'></i>
            <span>{{ number_format($book->rating ?? 0, 1) }}</span>
          </div>
          <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
            <div class="loading-indicator">
              <i class='bx bx-loader-alt'></i>
            </div>
          </div>
          <div class="info-container">
            <h3 class="info-title">{{ $book->title ?? '' }}</h3>
            <p class="info-author">{{ $book->author ?? '' }}</p>
            <p class="info-category">{{ $book->category->name ?? '' }}</p>
            <div class="button-container">
              <a class="view-btn" href="{{ route('view', $book->id) }}">
                <i class='bx bx-book-reader'></i> View
              </a>
              <form
                action="{{ $book->isInReadLaterOf(auth()->user()) ? route('readlater.delete', $book->id) : route('readlater.add', $book->id) }}"
                method="POST" style="display: contents;">
                @csrf
                @if ($book->isInReadLaterOf(auth()->user()))
                  @method('DELETE')
                @endif
                <button type="submit" class="favorite-btn">
                  <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach


    </div>

    <div class="pagination-container">
      {{ $data->onEachSide(1)->links() }}
    </div>

    {{-- Mobile Modals --}}
    <div class="mobile-modals-container">
      @foreach ($data as $book)
        @include('components.book-modal', ['book' => $book, 'showAdminActions' => false])
      @endforeach
    </div>

  </div>

  <script src="{{ asset('js/library.js') }}"></script>
  <script src="{{ asset('js/library-pdf.js') }}" type="module"></script>
</body>

</html>
