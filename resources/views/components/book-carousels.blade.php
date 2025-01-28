{{-- Newest Books Carousel --}}
<div class="newest-books-container">
  <div class="carousel-header">
    <h1 class="h1-text">NEWEST</h1>
    <div class="carousel-nav">
      <button class="carousel-button prev"><i class='bx bx-chevron-left'></i></button>
      <button class="carousel-button next"><i class='bx bx-chevron-right'></i></button>
    </div>
  </div>
  <div class="carousel-container">
    <div class="carousel-wrapper">
      @foreach ($recentBooks as $book)
        <div class="carousel-item" data-book-id="{{ $book->id }}">
          <div class="rating-badge">
            <i class='bx bxs-star'></i>
            <span>{{ number_format($book->rating ?? 0, 1) }}</span>
          </div>
          <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
          <div class="info-container">
            <h3 class="info-title">{{ $book->title }}</h3>
            <p class="info-author">{{ $book->author }}</p>
            <p class="info-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
            <div class="button-container">
              <a class="view-btn" href="{{ route('view', $book->id) }}">
                <i class='bx bx-book-reader'></i> View
              </a>
              <button class="favorite-btn" onclick="toggleFavorite({{ $book->id }})">
                <i class='bx bx-heart'></i>
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- Genre-specific Carousels --}}
@foreach ($preferredBooks as $genre => $books)
  <div class="newest-books-container">
    <div class="carousel-header">
      <h1 class="h1-text">{{ strtoupper($genre) }} BOOKS</h1>
      <div class="carousel-nav">
        <button class="carousel-button prev"><i class='bx bx-chevron-left'></i></button>
        <button class="carousel-button next"><i class='bx bx-chevron-right'></i></button>
      </div>
    </div>
    <div class="carousel-container">
      <div class="carousel-wrapper">
        @foreach ($books as $book)
          <div class="carousel-item" data-book-id="{{ $book->id }}">
            <div class="rating-badge">
              <i class='bx bxs-star'></i>
              <span>{{ number_format($book->rating ?? 0, 1) }}</span>
            </div>
            <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
            <div class="info-container">
              <h3 class="info-title">{{ $book->title }}</h3>
              <p class="info-author">{{ $book->author }}</p>
              <p class="info-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
              <div class="button-container">
                <a class="view-btn" href="{{ route('view', $book->id) }}">
                  <i class='bx bx-book-reader'></i> View
                </a>
                <button class="favorite-btn" onclick="toggleFavorite({{ $book->id }})">
                  <i class='bx bx-heart'></i>
                </button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endforeach

{{-- Mobile Modals (Outside carousel structure) --}}
<div class="mobile-modals-container">
  @foreach ($recentBooks as $book)
    <div class="mobile-modal" data-book-id="{{ $book->id }}">
      <div class="modal-content">
        <button class="modal-close"><i class='bx bx-x'></i></button>
        <div class="modal-book-info">
          <div class="modal-thumbnail">
            <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
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
          <a class="view-btn" href="{{ route('view', $book->id) }}">
            <i class='bx bx-book-reader'></i> Read Now
          </a>
          <button class="favorite-btn" onclick="toggleFavorite({{ $book->id }})">
            <i class='bx bx-heart'></i>
          </button>
        </div>
      </div>
    </div>
  @endforeach

  @foreach ($preferredBooks as $books)
    @foreach ($books as $book)
      <div class="mobile-modal" data-book-id="{{ $book->id }}">
        <div class="modal-content">
          <button class="modal-close"><i class='bx bx-x'></i></button>
          <div class="modal-book-info">
            <div class="modal-thumbnail">
              <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
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
            <a class="view-btn" href="{{ route('view', $book->id) }}">
              <i class='bx bx-book-reader'></i> Read Now
            </a>
            <button class="favorite-btn" onclick="toggleFavorite({{ $book->id }})">
              <i class='bx bx-heart'></i>
            </button>
          </div>
        </div>
      </div>
    @endforeach
  @endforeach
</div>

{{-- PDF Thumbnail Generation Script --}}
<script type="module">
  import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';

  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

  async function generateThumbnail(pdfPath) {
    try {
      const loadingTask = pdfjsLib.getDocument(pdfPath);
      const pdf = await loadingTask.promise;
      const page = await pdf.getPage(1);

      const viewport = page.getViewport({
        scale: 1
      });
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');

      canvas.width = viewport.width;
      canvas.height = viewport.height;

      const renderContext = {
        canvasContext: context,
        viewport: viewport
      };

      await page.render(renderContext).promise;

      const thumbnailImg = document.createElement('img');
      thumbnailImg.src = canvas.toDataURL();
      thumbnailImg.style.width = '100%';
      thumbnailImg.style.height = '100%';
      thumbnailImg.style.objectFit = 'cover';

      const thumbnailDivs = document.querySelectorAll('.thumbnail[data-pdfpath="' + pdfPath + '"]');
      thumbnailDivs.forEach(div => {
        div.innerHTML = '';
        div.appendChild(thumbnailImg.cloneNode(true));
      });
    } catch (error) {
      console.error("Error loading PDF:", error);
      const thumbnailDivs = document.querySelectorAll('.thumbnail[data-pdfpath="' + pdfPath + '"]');
      thumbnailDivs.forEach(div => {
        div.innerHTML =
          '<img src="{{ asset('images/pdf-icon.png') }}" style="width: 100%; height: 100%; object-fit: contain; padding: 20px;">';
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const uniquePdfPaths = new Set();

    document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function(thumbnailDiv) {
      uniquePdfPaths.add(thumbnailDiv.dataset.pdfpath);
    });

    uniquePdfPaths.forEach(function(pdfPath) {
      generateThumbnail(pdfPath);
    });

    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
      document.querySelectorAll('.carousel-item').forEach(item => {
        item.addEventListener('click', function(e) {
          if (e.target.closest('.view-btn') || e.target.closest('.favorite-btn')) {
            return;
          }

          const bookId = this.dataset.bookId;
          const modal = document.querySelector(`.mobile-modal[data-book-id="${bookId}"]`);

          if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
          }
        });
      });

      document.querySelectorAll('.modal-close').forEach(button => {
        button.addEventListener('click', function(e) {
          e.stopPropagation();
          const modal = this.closest('.mobile-modal');
          if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
          }
        });
      });

      document.querySelectorAll('.mobile-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
          if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = '';
          }
        });
      });
    }

    window.addEventListener('resize', function() {
      const isMobile = window.innerWidth <= 768;
      const activeModal = document.querySelector('.mobile-modal.active');

      if (!isMobile && activeModal) {
        activeModal.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });
</script>
