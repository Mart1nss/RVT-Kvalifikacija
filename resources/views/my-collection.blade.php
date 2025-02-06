<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Favorites</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/favorites-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
      <h1 class="text-container-title">MY COLLECTION</h1>
    </div>

    <div class="tabs-container">
      <button class="tab-btn active" data-tab="favorites">
        <i class='bx bxs-star'></i> Favorites
      </button>
      <button class="tab-btn" data-tab="readlater">
        <i class='bx bxs-bookmark'></i> Read Later
      </button>
    </div>

    <div id="favorites-tab" class="tab-content active">
      <div class="item-container">
        @if ($favorites->count() > 0)
          @foreach ($favorites as $favorite)
            <div class="pdf-item" data-book-id="{{ $favorite->product->id }}">
              <div class="rating-badge">
                <i class='bx bxs-star'></i>
                <span>{{ number_format($favorite->product->rating ?? 0, 1) }}</span>
              </div>
              <div class="thumbnail" data-pdfpath="/assets/{{ $favorite->product->file }}"></div>
              <div class="info-container">
                <h3 class="info-title">{{ $favorite->product->title }}</h3>
                <p class="info-author">{{ $favorite->product->author }}</p>
                <p class="info-category">{{ $favorite->product->category->name ?? '' }}</p>
                <div class="button-container">
                  <a class="view-btn" href="{{ route('view', $favorite->product->id) }}">
                    <i class='bx bx-book-reader'></i> View
                  </a>
                  <form action="{{ route('my-collection.delete', $favorite->product_id) }}" method="POST"
                    style="display: contents;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="favorite-btn">
                      <i class='bx bxs-star'></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <p
            style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">
            There are no favorites in your collection!</p>
        @endif
      </div>
    </div>

    <div id="readlater-tab" class="tab-content">
      <div class="item-container">
        @if ($readLater->count() > 0)
          @foreach ($readLater as $item)
            <div class="pdf-item" data-book-id="{{ $item->product->id }}">
              <div class="rating-badge">
                <i class='bx bxs-star'></i>
                <span>{{ number_format($item->product->rating ?? 0, 1) }}</span>
              </div>
              <div class="thumbnail" data-pdfpath="/assets/{{ $item->product->file }}"></div>
              <div class="info-container">
                <h3 class="info-title">{{ $item->product->title }}</h3>
                <p class="info-author">{{ $item->product->author }}</p>
                <p class="info-category">{{ $item->product->category->name ?? '' }}</p>
                <div class="button-container">
                  <a class="view-btn" href="{{ route('view', $item->product->id) }}">
                    <i class='bx bx-book-reader'></i> View
                  </a>
                  <form action="{{ route('readlater.delete', $item->product_id) }}" method="POST"
                    style="display: contents;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="favorite-btn">
                      <i class='bx bxs-bookmark'></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <p
            style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">
            There are no books in read later list!</p>
        @endif
      </div>
    </div>

    {{-- Mobile Modals --}}
    <div class="mobile-modals-container">
      @foreach ($favorites as $book)
        @include('components.book-modal', [
            'book' => $book->product,
            'showAdminActions' => false,
            'source' => 'favorites',
        ])
      @endforeach

      @foreach ($readLater as $book)
        @include('components.book-modal', [
            'book' => $book->product,
            'showAdminActions' => false,
            'source' => 'readlater',
        ])
      @endforeach
    </div>
  </div>

  <script type="module">
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

    // Track which thumbnails have been generated
    const generatedThumbnails = new Set();
    const loadingThumbnails = new Set();

    // Add loading indicator
    function showLoadingIndicator(thumbnailDiv) {
      thumbnailDiv.innerHTML = `
        <div class="loading-indicator" style="
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          background: #2a2a2a;
        ">
          <i class='bx bx-loader-alt' style="
            font-size: 2rem;
            color: white;
            animation: spin 1s linear infinite;
          "></i>
        </div>
      `;
    }

    // Intersection Observer for lazy loading
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const thumbnailDiv = entry.target;
          const pdfPath = thumbnailDiv.dataset.pdfpath;

          if (!generatedThumbnails.has(pdfPath) && !loadingThumbnails.has(pdfPath)) {
            loadingThumbnails.add(pdfPath);
            showLoadingIndicator(thumbnailDiv);
            generateThumbnail(pdfPath);
          }
        }
      });
    }, {
      rootMargin: '100px 0px', // Increased preload margin
      threshold: 0.1 // Trigger when even 10% of the element is visible
    });

    async function generateThumbnail(pdfPath) {
      try {
        const loadingTask = pdfjsLib.getDocument(pdfPath);

        // Add error handling for failed loads
        loadingTask.promise.catch(error => {
          console.error("Error loading PDF:", error);
          document.querySelectorAll(`.thumbnail[data-pdfpath="${pdfPath}"]`).forEach(div => {
            div.innerHTML = `
              <div style="
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #2a2a2a;
              ">
                <i class='bx bx-error' style="font-size: 2rem; color: #ff4444;"></i>
              </div>
            `;
          });
          loadingThumbnails.delete(pdfPath);
          return;
        });

        const pdf = await loadingTask.promise;
        const page = await pdf.getPage(1);

        // Calculate optimal dimensions while maintaining aspect ratio
        const viewport = page.getViewport({
          scale: 1.0
        });
        const MAX_WIDTH = 400; // Maximum width for thumbnails
        const scale = Math.min(1.0, MAX_WIDTH / viewport.width);
        const scaledViewport = page.getViewport({
          scale
        });

        const canvas = document.createElement('canvas');
        canvas.width = scaledViewport.width;
        canvas.height = scaledViewport.height;

        const context = canvas.getContext('2d', {
          alpha: false,
          desynchronized: true // Potential performance improvement
        });

        await page.render({
          canvasContext: context,
          viewport: scaledViewport,
          intent: 'display' // Optimize for display
        }).promise;

        // Create optimized thumbnail
        const thumbnailImg = document.createElement('img');
        thumbnailImg.style.width = '100%';
        thumbnailImg.style.height = '100%';
        thumbnailImg.style.objectFit = 'cover';
        thumbnailImg.decoding = 'async'; // Optimize image decoding

        // Use better quality for JPEG
        thumbnailImg.src = canvas.toDataURL('image/jpeg', 0.85);

        // Update all instances
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
        console.error("Error generating thumbnail:", error);
        loadingThumbnails.delete(pdfPath);
      }
    }

    // Add loading indicator styles
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
      }
    `;
    document.head.appendChild(style);

    // Observe all thumbnail divs
    document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(thumbnailDiv => {
      observer.observe(thumbnailDiv);
    });

    // Memory management
    let cleanupInterval;

    function startCleanupInterval() {
      cleanupInterval = setInterval(() => {
        if (generatedThumbnails.size > 50) { // Adjust based on your needs
          const thumbnailsToRemove = Array.from(generatedThumbnails).slice(0, 20);
          thumbnailsToRemove.forEach(path => generatedThumbnails.delete(path));
        }
      }, 30000); // Check every 30 seconds
    }

    // Start cleanup interval when page is visible
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        clearInterval(cleanupInterval);
      } else {
        startCleanupInterval();
      }
    });

    startCleanupInterval();

    // Cleanup when leaving the page
    window.addEventListener('unload', () => {
      observer.disconnect();
      generatedThumbnails.clear();
      loadingThumbnails.clear();
      clearInterval(cleanupInterval);
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabBtns = document.querySelectorAll('.tab-btn');
      const tabContents = document.querySelectorAll('.tab-content');
      const STORAGE_KEY = 'activeLibraryTab';

      // Function to update active tab
      function setActiveTab(tabName) {
        tabBtns.forEach(btn => {
          if (btn.dataset.tab === tabName) {
            btn.classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
          } else {
            btn.classList.remove('active');
            document.getElementById(`${btn.dataset.tab}-tab`).classList.remove('active');
          }
        });
      }

      // Get URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      const tabFromUrl = urlParams.get('tab');

      // Get tab from old input if available
      const oldInput = @json(old('tab'));

      // Determine which tab to show (priority: old input > URL param > localStorage > default)
      const activeTab = oldInput || tabFromUrl || localStorage.getItem(STORAGE_KEY) || 'favorites';
      setActiveTab(activeTab);
      localStorage.setItem(STORAGE_KEY, activeTab);

      // Handle tab clicks
      tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          const tabName = btn.dataset.tab;
          setActiveTab(tabName);
          localStorage.setItem(STORAGE_KEY, tabName);
          // Update URL without reloading the page
          const newUrl = new URL(window.location);
          newUrl.searchParams.set('tab', tabName);
          window.history.pushState({}, '', newUrl);
        });
      });

      // Handle form submissions
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
          // Add the current tab as a hidden field to the form
          const currentTab = localStorage.getItem(STORAGE_KEY) || 'favorites';
          const hiddenField = document.createElement('input');
          hiddenField.type = 'hidden';
          hiddenField.name = 'current_tab';
          hiddenField.value = currentTab;
          this.appendChild(hiddenField);
        });
      });

      // Clear localStorage only when actually leaving the favorites page
      window.addEventListener('beforeunload', function(e) {
        if (window.location.pathname === '/favorites' && e.target.activeElement.tagName !== 'FORM') {
          localStorage.removeItem(STORAGE_KEY);
        }
      });

      // Mobile modal functionality
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

</body>

</html>
