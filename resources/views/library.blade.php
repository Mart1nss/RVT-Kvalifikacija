<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Library</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css"
    integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="text-container">
      <h1 class="text-container-title">Library</h1>
      <div class="search-filter-container">
        <div class="search-container">
          <input type="text" id="search-input" placeholder="Search books...">
        </div>
        <div class="genre-filter-container">
          <div class="genre-dropdown">
            <button class="dropdown-btn">Filter by Genres</button>
            <ul class="dropdown-content">
              <!-- zanri -->
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="item-container">
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


    </div>

    <div class="pagination-container">
      {{ $data->onEachSide(1)->links() }}
    </div>

    {{-- Mobile Modals --}}
    <div class="mobile-modals-container">
      @foreach ($data as $book)
        @include('components.book-modal', ['book' => $book, 'showAdminActions' => false])
      @endforeach
    </div>

  </div>

  <script type="module">
    import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

    // Track which thumbnails have been generated and are loading
    const generatedThumbnails = new Set();
    const loadingThumbnails = new Set();
    const failedThumbnails = new Set();

    // Intersection Observer for lazy loading
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const thumbnailDiv = entry.target;
          const pdfPath = thumbnailDiv.dataset.pdfpath;

          if (!generatedThumbnails.has(pdfPath) && !loadingThumbnails.has(pdfPath) && !failedThumbnails.has(
              pdfPath)) {
            loadingThumbnails.add(pdfPath);
            generateThumbnail(pdfPath);
          }
        }
      });
    }, {
      rootMargin: '100px 0px', // Preload margin
      threshold: 0.1 // Trigger when 10% visible
    });

    async function generateThumbnail(pdfPath) {
      try {
        const loadingTask = pdfjsLib.getDocument(pdfPath);
        let timeoutId = setTimeout(() => {
          loadingTask.destroy();
          handleThumbnailError(pdfPath, new Error('Loading timeout'));
        }, 10000); // 10 second timeout

        const pdf = await loadingTask.promise;
        clearTimeout(timeoutId);

        const page = await pdf.getPage(1);

        // Calculate optimal dimensions
        const viewport = page.getViewport({
          scale: 1.0
        });
        const MAX_WIDTH = 400;
        const scale = Math.min(1.0, MAX_WIDTH / viewport.width);
        const scaledViewport = page.getViewport({
          scale
        });

        const canvas = document.createElement('canvas');
        canvas.width = scaledViewport.width;
        canvas.height = scaledViewport.height;

        const context = canvas.getContext('2d', {
          alpha: false,
          desynchronized: true
        });

        await page.render({
          canvasContext: context,
          viewport: scaledViewport,
          intent: 'display'
        }).promise;

        // Create and optimize thumbnail
        const thumbnailImg = new Image();
        thumbnailImg.decoding = 'async';
        thumbnailImg.loading = 'lazy';
        thumbnailImg.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';

        // Use better quality JPEG with reasonable compression
        thumbnailImg.src = canvas.toDataURL('image/jpeg', 0.85);

        // Update all instances of this thumbnail
        document.querySelectorAll(`.thumbnail[data-pdfpath="${pdfPath}"]`).forEach(div => {
          div.innerHTML = '';
          div.appendChild(thumbnailImg.cloneNode(true));
        });

        // Cleanup
        canvas.width = 0;
        canvas.height = 0;
        context.clearRect(0, 0, 0, 0);
        page.cleanup();
        pdf.destroy();
        loadingTask.destroy();

        generatedThumbnails.add(pdfPath);
        loadingThumbnails.delete(pdfPath);

      } catch (error) {
        handleThumbnailError(pdfPath, error);
      }
    }

    function handleThumbnailError(pdfPath, error) {
      console.error("Error generating thumbnail:", error);
      document.querySelectorAll(`.thumbnail[data-pdfpath="${pdfPath}"]`).forEach(div => {
        div.innerHTML = `
          <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #2a2a2a;">
            <i class='bx bx-error' style="font-size: 2rem; color: #ff4444;"></i>
          </div>
        `;
      });
      loadingThumbnails.delete(pdfPath);
      failedThumbnails.add(pdfPath);
    }

    // Add styles
    const style = document.createElement('style');
    style.textContent = `
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
      .thumbnail {
        position: relative;
        background: #2a2a2a;
      }
      .loading-indicator {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2a2a2a;
      }
      .loading-indicator i {
        font-size: 2rem;
        color: white;
        animation: spin 1s linear infinite;
      }
    `;
    document.head.appendChild(style);

    // Observe thumbnails
    document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(thumbnailDiv => {
      observer.observe(thumbnailDiv);
    });

    // Memory management
    let cleanupInterval;
    const MAX_CACHED_THUMBNAILS = 50;

    function startCleanupInterval() {
      cleanupInterval = setInterval(() => {
        if (generatedThumbnails.size > MAX_CACHED_THUMBNAILS) {
          const thumbnailsToRemove = Array.from(generatedThumbnails).slice(0, 20);
          thumbnailsToRemove.forEach(path => {
            generatedThumbnails.delete(path);
            document.querySelectorAll(`.thumbnail[data-pdfpath="${path}"]`).forEach(div => {
              if (!div.isIntersecting) {
                div.innerHTML = `
                  <div class="loading-indicator">
                    <i class='bx bx-loader-alt'></i>
                  </div>
                `;
              }
            });
          });
        }
      }, 30000); // Check every 30 seconds
    }

    // Visibility handling
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        clearInterval(cleanupInterval);
      } else {
        startCleanupInterval();
      }
    });

    startCleanupInterval();

    // Cleanup on page leave
    window.addEventListener('unload', () => {
      observer.disconnect();
      generatedThumbnails.clear();
      loadingThumbnails.clear();
      failedThumbnails.clear();
      clearInterval(cleanupInterval);
    });
  </script>

  <script>
    // Debounced search function
    function debounce(func, wait) {
      let timeout;
      return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    }

    $(document).ready(function() {
      const searchBooks = debounce(function(searchTerm) {
        $(".pdf-item").each(function() {
          const title = $(this).find(".info-title").text().toLowerCase();
          const author = $(this).find(".info-author").text().toLowerCase();

          if (title.includes(searchTerm) || author.includes(searchTerm)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }, 300); // 300ms debounce

      $("#search-input").on("keyup", function() {
        const searchTerm = $(this).val().toLowerCase();
        searchBooks(searchTerm);
      });
    });
  </script>

  <script>
    // Optimized genre filtering
    const genreFilterState = new Set();

    function filterBooks() {
      const books = document.querySelectorAll('.pdf-item');
      books.forEach(book => {
        const genre = book.dataset.genre;
        book.style.display = genreFilterState.size === 0 || genreFilterState.has(genre) ? '' : 'none';
      });
    }

    // Handle dropdown with event delegation
    document.addEventListener('DOMContentLoaded', function() {
      const dropdownBtn = document.querySelector('.dropdown-btn');
      const dropdownContent = document.querySelector('.dropdown-content');

      document.addEventListener('click', function(e) {
        if (e.target.matches('.dropdown-btn')) {
          dropdownContent.classList.toggle('show');
        } else if (!e.target.closest('.dropdown-content')) {
          dropdownContent.classList.remove('show');
        }
      });

      dropdownContent.addEventListener('change', function(e) {
        if (e.target.matches('input[type="checkbox"]')) {
          const genre = e.target.value;
          if (e.target.checked) {
            genreFilterState.add(genre);
          } else {
            genreFilterState.delete(genre);
          }
          filterBooks();
        }
      });
    });

    // Fetch genres with caching
    const genresCache = sessionStorage.getItem('genres');
    if (genresCache) {
      populateGenres(JSON.parse(genresCache));
    } else {
      fetch('/get-genres')
        .then(response => response.json())
        .then(genres => {
          sessionStorage.setItem('genres', JSON.stringify(genres));
          populateGenres(genres);
        });
    }

    function populateGenres(genres) {
      const dropdownContent = document.querySelector('.dropdown-content');
      const fragment = document.createDocumentFragment();

      genres.forEach(genre => {
        if (genre) {
          const li = document.createElement('li');
          li.innerHTML = `
            <label class="genre-checkbox-container">
              <input class="genre-checkbox" type="checkbox" value="${genre}">
              ${genre}
            </label>
          `;
          fragment.appendChild(li);
        }
      });

      dropdownContent.appendChild(fragment);
    }
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const isMobile = window.innerWidth <= 768;

      if (isMobile) {
        document.querySelectorAll('.pdf-item').forEach(item => {
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
            closeAllMobileModals();
          });
        });

        document.querySelectorAll('.mobile-modal').forEach(modal => {
          modal.addEventListener('click', function(e) {
            if (e.target === this) {
              closeAllMobileModals();
            }
          });
        });
      }

      window.addEventListener('resize', function() {
        const isMobile = window.innerWidth <= 768;
        if (!isMobile) {
          closeAllMobileModals();
        }
      });
    });

    function closeAllMobileModals() {
      document.querySelectorAll('.mobile-modal.active').forEach(modal => {
        modal.classList.remove('active');
      });
      document.body.style.overflow = '';
    }
  </script>

</body>

</html>
