<div class="pdf-item" data-book-id="{{ $book->id }}" data-genre="{{ $book->category->name ?? '' }}">
  <div class="rating-badge">
    <i class='bx bxs-star'></i>
    <span>{{ number_format($book->rating ?? 0, 1) }}</span>
  </div>

  @if ($showAdminActions)
    <div class="admin-actions">
      <button class="admin-btn edit-btn" wire:click="editBook" title="Edit">
        <i class='bx bx-edit-alt'></i>
      </button>
      <button type="button" class="admin-btn delete-btn" wire:click="confirmDelete" title="Delete">
        <i class='bx bx-trash'></i>
      </button>
      <a href="{{ route('download', $book->file) }}" class="admin-btn download-btn" title="Download">
        <i class='bx bxs-download'></i>
      </a>
    </div>
  @endif

  <div class="thumbnail">
    <img src="{{ asset('book-thumbnails/' . str_replace('.pdf', '.jpg', $book->file)) }}" alt="{{ $book->title }}"
      loading="lazy" class="book-thumbnail">
  </div>

  <div class="info-container">
    <h3 class="info-title">{{ $book->title }}</h3>
    <p class="info-author">{{ $book->author }}</p>
    <p class="info-category">{{ $book->category->name ?? '' }}</p>
    <div class="button-container">
      <a class="view-btn" href="{{ route('view', $book->id) }}">
        <i class='bx bx-book-reader'></i> Read
      </a>

      @if ($source === 'favorites')
        <button type="button" class="readlater-btn" wire:click="deleteFromFavorites">
          <i class='bx bxs-star'></i>
        </button>
      @elseif($source === 'readlater')
        <button type="button" class="readlater-btn" wire:click="deleteFromReadLater">
          <i class='bx bxs-bookmark'></i>
        </button>
      @else
        <button type="button" class="readlater-btn" wire:click="toggleReadLater" wire:loading.class="loading"
          wire:loading.attr="disabled">
          <i class='bx {{ $isInReadLater ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
        </button>
      @endif
    </div>
  </div>

  <style>
    .book-thumbnail {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
</div>
