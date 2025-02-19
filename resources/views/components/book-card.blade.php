@props([
    'book',
    'showAdminActions' => false,
    'source' => 'library', // Can be 'library', 'favorites', or 'readlater'
])

<div class="pdf-item" data-book-id="{{ $book->id }}" data-genre="{{ $book->category->name ?? '' }}"
  x-data="{
      isMobile: window.innerWidth <= 768,
      showModal: false,
      isInReadLater: {{ $book->isInReadLaterOf(auth()->user()) ? 'true' : 'false' }},
      isLoading: false,
      async toggleReadLater() {
          if (this.isLoading) return;
          this.isLoading = true;
  
          const url = this.isInReadLater ?
              '{{ route('readlater.delete', $book->id) }}' :
              '{{ route('readlater.add', $book->id) }}';
  
          try {
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
              });
  
              if (response.ok) {
                  this.isInReadLater = !this.isInReadLater;
                  // Dispatch a custom event for notifications
                  const message = this.isInReadLater ? 'Book added to read later list' : 'Book removed from read later list';
                  window.dispatchEvent(new CustomEvent('show-alert', {
                      detail: { message, type: 'success' }
                  }));
              } else {
                  throw new Error('Failed to update read later status');
              }
          } catch (error) {
              window.dispatchEvent(new CustomEvent('show-alert', {
                  detail: { message: 'Failed to update read later status', type: 'error' }
              }));
          } finally {
              this.isLoading = false;
          }
      },
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
        <button type="button" class="readlater-btn" :class="{ 'loading': isLoading }" @click.prevent="toggleReadLater"
          :disabled="isLoading">
          <i class='bx' :class="isInReadLater ? 'bxs-bookmark' : 'bx-bookmark'"></i>
        </button>
      @endif
    </div>
  </div>
</div>

<style>
  .readlater-btn.loading {
    opacity: 0.7;
    cursor: not-allowed;
  }
</style>
