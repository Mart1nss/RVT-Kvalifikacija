 <link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
{{-- Book Modal Component --}}
<div class="mobile-modal" data-book-id="{{ $book->id }}">
  <div class="modal-content">
    <button class="modal-close"><i class='bx bx-x'></i></button>
    <div class="modal-book-info">
      <div class="modal-thumbnail">
        <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
          <div class="loading-indicator">
            <i class='bx bx-loader-alt'></i>
          </div>
        </div>
      </div>
      <div class="modal-details">
        <h3>{{ $book->title }}</h3>
        <p class="modal-author">{{ $book->author }}</p>
        <p class="modal-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
        <div class="modal-rating">
          <i class='bx bxs-star'></i>
          <span>{{ number_format($book->rating ?? 0, 1) }}</span>
        </div>
      </div>
    </div>
    <div class="modal-buttons">
      <div class="modal-action-row">
        <a class="view-btn" href="{{ route('view', $book->id) }}">
          <i class='bx bx-book-reader'></i> Read
        </a>
        <div class="action-buttons-group">
          @if (isset($source) && $source === 'favorites')
            <form action="{{ route('my-collection.delete', $book->id) }}" method="POST" style="display: contents;">
              @csrf
              @method('DELETE')
              <button type="submit" class="action-btn">
                <i class='bx bxs-star'></i>
              </button>
            </form>
          @else
            <form
              action="{{ $book->isInReadLaterOf(auth()->user()) ? route('readlater.delete', $book->id) : route('readlater.add', $book->id) }}"
              method="POST" style="display: contents;">
              @csrf
              @if ($book->isInReadLaterOf(auth()->user()))
                @method('DELETE')
              @endif
              <button type="submit" class="action-btn">
                <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
              </button>
            </form>
          @endif

          @if (isset($showAdminActions) && $showAdminActions)
            <button class="action-btn edit-btn" onclick="openEditModal({{ $book->id }}); closeAllMobileModals();">
              <i class='bx bx-edit-alt'></i>
            </button>
            <a href="{{ route('download', $book->file) }}" class="action-btn download-btn">
              <i class='bx bxs-download'></i>
            </a>
            <button class="action-btn delete-btn"
              onclick="confirmDelete('{{ $book->title }}', '{{ $book->author }}', {{ $book->id }}); closeAllMobileModals();">
              <i class='bx bx-trash'></i>
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
