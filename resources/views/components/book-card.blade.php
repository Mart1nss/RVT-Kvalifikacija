@props([
    'book',
    'showAdminActions' => false,
    'source' => 'library', // Can be 'library', 'favorites', or 'readlater'
])

<div class="pdf-item" data-book-id="{{ $book->id }}" data-genre="{{ $book->category->name ?? '' }}"
  x-data="{
      isMobile: window.innerWidth <= 768,
      showModal: false,
      init() {
          window.addEventListener('resize', () => {
              this.isMobile = window.innerWidth <= 768;
              if (!this.isMobile) this.showModal = false;
          });
      }
  }"
  @click="if (isMobile && !$event.target.closest('.view-btn') && !$event.target.closest('.readlater-btn')) {
       $dispatch('open-modal', { bookId: {{ $book->id }} })
     }">
  <div class="rating-badge">
    <i class='bx bxs-star'></i>
    <span>{{ number_format($book->rating ?? 0, 1) }}</span>
  </div>

  @if ($showAdminActions)
    <div class="admin-actions">
      <button class="admin-btn edit-btn" onclick="openEditModal({{ $book->id }})" title="Edit">
        <i class='bx bx-edit-alt'></i>
      </button>
      <form id="deleteForm{{ $book->id }}" action="{{ route('delete', $book->id) }}" method="POST"
        style="display: contents;">
        @csrf
        @method('DELETE')
        <button type="button" class="admin-btn delete-btn" title="Delete"
          onclick="confirmDelete('{{ $book->title }}', '{{ $book->author }}', {{ $book->id }})">
          <i class='bx bx-trash'></i>
        </button>
      </form>
      <a href="{{ route('download', $book->file) }}" class="admin-btn download-btn" title="Download">
        <i class='bx bxs-download'></i>
      </a>
    </div>
  @endif

  <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
    <div class="loading-indicator">
      <i class='bx bx-loader-alt'></i>
    </div>
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
        <form action="{{ route('my-collection.delete', $book->id) }}" method="POST" style="display: contents;">
          @csrf
          @method('DELETE')
          <button type="submit" class="readlater-btn">
            <i class='bx bxs-star'></i>
          </button>
        </form>
      @elseif($source === 'readlater')
        <form action="{{ route('readlater.delete', $book->id) }}" method="POST" style="display: contents;">
          @csrf
          @method('DELETE')
          <button type="submit" class="readlater-btn">
            <i class='bx bxs-bookmark'></i>
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
          <button type="submit" class="readlater-btn">
            <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
          </button>
        </form>
      @endif
    </div>
  </div>
</div>
