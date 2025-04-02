<div>
  <div class="text-container">
    <h1 class="text-container-title">Library</h1>
    @livewire('books.filter-section', [
        'sort' => $this->sort,
        'isAdmin' => false,
        'totalBooks' => $totalBooks,
    ])
  </div>

  <div id="books-container">
    <div class="item-container">
      @foreach ($books as $book)
        @livewire(
            'books.book-card',
            [
                'book' => $book,
                'source' => 'library',
            ],
            key('book-' . $book->id)
        )
      @endforeach
    </div>

    <div class="pagination-container">
      {{ $books->onEachSide(1)->links('vendor.pagination.tailwind') }}
    </div>
  </div>

  {{-- Mobile Modals --}}
  <div class="mobile-modals-container">
    @foreach ($books as $book)
      @include('components.book-modal', [
          'book' => $book,
          'showAdminActions' => false,
          'source' => 'library',
      ])
    @endforeach
  </div>
</div>
