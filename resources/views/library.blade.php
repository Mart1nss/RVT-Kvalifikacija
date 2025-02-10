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
  <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="text-container">
      <h1 class="text-container-title">Library</h1>
      <x-filter-section :sort="$sort" />
    </div>

    <div id="books-container">
      <div class="item-container">
        @foreach ($data as $book)
          <x-book-card :book="$book" :source="'library'" />
        @endforeach
      </div>

      <div class="pagination-container">
        {{ $data->onEachSide(1)->links() }}
      </div>
    </div>

    {{-- Mobile Modals --}}
    <div class="mobile-modals-container">
      @foreach ($data as $book)
        @include('components.book-modal', ['book' => $book, 'showAdminActions' => false])
      @endforeach
    </div>
  </div>

  <script src="{{ asset('js/library-pdf.js') }}" type="module"></script>
</body>

</html>
