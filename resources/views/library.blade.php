<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Library</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
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
            <!-- Genres -->
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
            <li data-value="rating_asc">Rating Asc</li>
            <li data-value="rating_desc">Rating Desc</li>
          </ul>
          <select id="sortSelect" style="display: none;">
            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest</option>
            <option value="title_asc" {{ $sort == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
            <option value="title_desc" {{ $sort == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
            <option value="author_asc" {{ $sort == 'author_asc' ? 'selected' : '' }}>Author (A-Z)</option>
            <option value="author_desc" {{ $sort == 'author_desc' ? 'selected' : '' }}>Author (Z-A)</option>
            <option value="rating_asc" {{ $sort == 'rating_asc' ? 'selected' : '' }}>Rating Asc</option>
            <option value="rating_desc" {{ $sort == 'rating_desc' ? 'selected' : '' }}>Rating Desc</option>
          </select>
        </div>

      </div>

      <div class="filter-info-row" style="display: none;">
        <span class="total-count"><span id="book-count">0</span> books</span>
        <div id="active-filters">
          <!-- Active genre filters -->
        </div>
        <button class="clear-filters-btn" style="display: none;">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      </div>

    </div>

    <div class="item-container">
      @foreach ($data as $book)
        <x-book-card :book="$book" :source="'library'" />
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
