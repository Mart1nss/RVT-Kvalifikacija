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

@if ($data->hasPages())
  <div class="pagination-container">
    {{ $data->onEachSide(1)->links() }}
  </div>
@endif
