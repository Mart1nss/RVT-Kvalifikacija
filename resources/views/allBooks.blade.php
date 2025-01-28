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
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Library</h1>
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
          <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
          <div class="info-container">
            <h3 class="info-title">{{ $book->title ?? '' }}</h3>
            <p class="info-author">{{ $book->author ?? '' }}</p>
            <p class="info-category">{{ $book->category->name ?? '' }}</p>
            <div class="button-container">
              <a class="view-btn" href="{{ route('view', $book->id) }}">
                <i class='bx bx-book-reader'></i> View
              </a>
              <form action="{{ route('favorites.add', $book->id) }}" method="POST" style="display: contents;">
                @csrf
                <button type="submit" class="favorite-btn">
                  <i class='bx bx-heart'></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Mobile Modals --}}
    <div class="mobile-modals-container">
      @foreach ($data as $book)
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
              <form action="{{ route('favorites.add', $book->id) }}" method="POST" style="display: contents;">
                @csrf
                <button type="submit" class="favorite-btn">
                  <i class='bx bx-heart'></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

  </div>

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


  <script>
    // Book Search
    $(document).ready(function() {
      $("#search-input").on("keyup", function() {
        let searchTerm = $(this).val().toLowerCase();

        $(".pdf-item").each(function() {
          let title = $(this).find(".info-container h5:first").text().toLowerCase();
          if (title.includes(searchTerm)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });
    });
  </script>



  <script>
    function filterBooks() {
      const selectedGenres = [];
      const checkboxes = document.querySelectorAll('.dropdown-content input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
          selectedGenres.push(checkbox.value);
        }
      });

      const books = document.querySelectorAll('.pdf-item');
      books.forEach(book => {
        const genre = book.dataset.genre;
        if (selectedGenres.length === 0 || selectedGenres.includes(genre)) {
          book.style.display = '';
        } else {
          book.style.display = 'none';
        }
      });
    }

    // Fetch and populate genres
    fetch('/get-genres')
      .then(response => response.json())
      .then(genres => {
        const dropdownContent = document.querySelector('.dropdown-content');
        genres.forEach(genre => {
          if (genre) { // Only add non-null genres
            const li = document.createElement('li');
            li.innerHTML = `
                      <label class="genre-checkbox-container">
                          <input class="genre-checkbox" type="checkbox" value="${genre}" onchange="filterBooks()">
                          ${genre}
                      </label>
                  `;
            dropdownContent.appendChild(li);
          }
        });
      });
  </script>

</body>

</html>
