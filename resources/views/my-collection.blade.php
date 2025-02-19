<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Collection</title>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/favorites-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="text-container">
      <h1 class="text-container-title">MY COLLECTION</h1>
    </div>

    <div class="tabs-container">
      <a href="{{ route('my-collection', ['tab' => 'favorites']) }}"
        class="tab-btn {{ $tab === 'favorites' ? 'active' : '' }}">
        <i class='bx bxs-star'></i> Favorites
      </a>
      <a href="{{ route('my-collection', ['tab' => 'readlater']) }}"
        class="tab-btn {{ $tab === 'readlater' ? 'active' : '' }}">
        <i class='bx bxs-bookmark'></i> Read Later
      </a>
    </div>

    @if ($tab === 'favorites')
      <div class="item-container">
        @if ($favorites->count() > 0)
          @foreach ($favorites as $favorite)
            <x-book-card :book="$favorite->product" :source="'favorites'" />
          @endforeach
        @else
          <p class="empty-message">There are no favorites in your collection!</p>
        @endif
      </div>
    @else
      <div class="item-container">
        @if ($readLater->count() > 0)
          @foreach ($readLater as $item)
            <x-book-card :book="$item->product" :source="'readlater'" />
          @endforeach
        @else
          <p class="empty-message">There are no books in read later list!</p>
        @endif
      </div>
    @endif

    {{-- Mobile Modals --}}
    <div class="mobile-modals-container">
      @if ($tab === 'favorites')
        @foreach ($favorites as $book)
          @include('components.book-modal', [
              'book' => $book->product,
              'showAdminActions' => false,
              'source' => 'favorites',
          ])
        @endforeach
      @else
        @foreach ($readLater as $book)
          @include('components.book-modal', [
              'book' => $book->product,
              'showAdminActions' => false,
              'source' => 'readlater',
          ])
        @endforeach
      @endif
    </div>
  </div>

  <script src="{{ asset('js/library-pdf.js') }}" type="module"></script>

</body>

</html>
