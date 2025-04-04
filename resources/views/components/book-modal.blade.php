@props(['book', 'showAdminActions' => false, 'source' => null])

<link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
{{-- Book Modal Component --}}
<div class="mobile-modal" data-book-id="{{ $book->id }}" x-data="{
    isOpen: false,
    closing: false,
    isInReadLater: {{ $book->isInReadLaterOf(auth()->user()) ? 'true' : 'false' }},
    isLoading: false,
    async toggleReadLater() {
        if (this.isLoading) return;
        this.isLoading = true;

        const url = this.isInReadLater ?
            '{{ route('readlater.delete', $book->id) }}' :
            '{{ route('readlater.add', $book->id) }}';

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
        if (this.isInReadLater) {
            formData.append('_method', 'DELETE');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        }).catch(() => {
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { message: 'Failed to update read later status', type: 'error' }
            }));
            return null;
        });

        this.isLoading = false;

        if (response && response.ok) {
            this.isInReadLater = !this.isInReadLater;
            const message = this.isInReadLater ? 'Book added to read later list' : 'Book removed from read later list';
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { message, type: 'success' }
            }));
        }
    },
    close() {
        this.closing = true;
        setTimeout(() => {
            this.isOpen = false;
            this.closing = false;
            document.body.style.overflow = '';
        }, 300);
    },
    triggerEditModal(bookId) {
        window.dispatchEvent(new CustomEvent('openEditModal', {
            detail: { bookId: bookId }
        }));
    },
    triggerDeleteConfirm(title, author, bookId) {
        window.dispatchEvent(new CustomEvent('confirmDelete', {
            detail: { title: title, author: author, bookId: bookId }
        }));
    }
}" x-show="isOpen"
  :class="{ 'active': isOpen, 'closing': closing }"
  @open-modal.window="if ($event.detail.bookId === {{ $book->id }}) {
       isOpen = true;
       document.body.style.overflow = 'hidden';
     }"
  @click="if ($event.target === $el) close()" @keydown.escape.window="close()">
  <div class="modal-content">
    <button class="modal-close" @click="close()"><i class='bx bx-x'></i></button>
    <div class="modal-book-info">
      <div class="modal-thumbnail">
        <div class="thumbnail">
          <img src="{{ asset('book-thumbnails/' . str_replace('.pdf', '.jpg', $book->file)) }}"
            alt="{{ $book->title }}" loading="lazy" class="book-thumbnail">
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
          @if ($source === 'favorites')
            <form action="{{ route('my-collection.delete', $book->id) }}" method="POST" style="display: contents;">
              @csrf
              @method('DELETE')
              <button type="submit" class="action-btn">
                <i class='bx bxs-star'></i>
              </button>
            </form>
          @elseif($source === 'readlater')
            <form action="{{ route('readlater.delete', $book->id) }}" method="POST" style="display: contents;">
              @csrf
              @method('DELETE')
              <button type="submit" class="action-btn">
                <i class='bx bxs-bookmark'></i>
              </button>
            </form>
          @else
            <button type="button" class="action-btn" :class="{ 'loading': isLoading }" @click.prevent="toggleReadLater"
              :disabled="isLoading">
              <i class='bx' :class="isInReadLater ? 'bxs-bookmark' : 'bx-bookmark'"></i>
            </button>
          @endif

          @if ($showAdminActions)
            <button class="action-btn edit-btn" @click="triggerEditModal({{ $book->id }}); close()">
              <i class='bx bx-edit-alt'></i>
            </button>
            <a href="{{ route('download', $book->file) }}" class="action-btn download-btn">
              <i class='bx bxs-download'></i>
            </a>
            <button class="action-btn delete-btn"
              @click="triggerDeleteConfirm('{{ $book->title }}', '{{ $book->author }}', {{ $book->id }}); close()">
              <i class='bx bx-trash'></i>
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .action-btn.loading {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .book-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
</style>
