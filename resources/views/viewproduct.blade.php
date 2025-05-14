<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>View Book</title>
  <link type="text/css" rel="stylesheet" href="{{ asset('css/pdf-view.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="container">
    <button id="exit-focus-mode" class="exit-focus-mode" style="display: none;">
      <i class='bx bx-x-circle'></i> FOCUS MODE
    </button>

    <div class="pdf-viewer-wrapper">
      <div id="pdf-toolbar">
        <button id="zoom-in"><i class='bx bx-zoom-in'></i></button>
        <button id="zoom-out"><i class='bx bx-zoom-out'></i></button>
        <span>Page: <span id="page-num">1</span> / <span id="page-count"></span></span>
        <button id="bookmark-btn" data-product-id="{{ $data->id }}">
          <i class='bx bx-bookmark-alt'></i> <!-- Default icon -->
        </button>
        <button id="read-btn" data-product-id="{{ $data->id }}">
          <i class='bx bx-check-circle'></i> <!-- Default icon -->
        </button>
      </div>

      <div class="container">
        <div id="pdf-container"></div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
    <script src="{{ asset('js/pdfViewer.js') }}"></script>

    <!-- Initialize PDF Viewer -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const pdfUrl = "/book-thumbnail/{{ $data->file }}";
        initPDFViewer(pdfUrl, handlePdfLoad);
      });
    </script>

    <button id="toggle-button" class="fab">
      <i class="toggle-icon bx bx-notepad"></i>
    </button>

    <div id="notes-div" class="notes-div">
      <div class="notes-name">
        <p class="notes-p">notes</p>
        <div class="character-counter"><span id="char-count">0</span>/7500</div>
      </div>
      <textarea id="note-area" maxlength="7500" placeholder="Type anything here..."></textarea>
    </div>
  </div>

  <section id="underPdf" class="container-under-pdf-view">
    <div class="metadata">
      <div class="book-info">
        <h2>{{ $data->title }} </h2>
        <h3 style="color: white; display: block;"> {{ $data->author }}</h3>
      </div>
      <div class="action-buttons">
        <form class="favorite-form" x-data="{ isFavorited: {{ $product->isFavoritedBy(auth()->user()) ? 'true' : 'false' }} }"
          @submit.prevent="
            fetch($el.getAttribute('action'), {
              method: $el.getAttribute('data-method'),
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {
              isFavorited = !isFavorited;
              window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { message: data.message, type: 'success' }
              }));
            })
            .catch(error => {
              window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { message: 'An error occurred', type: 'error' }
              }));
            })"
          :action="isFavorited ? `{{ route('my-collection.delete', $product->id) }}` :
              `{{ route('my-collection.add', $product->id) }}`"
          :data-method="isFavorited ? 'DELETE' : 'POST'">
          @csrf
          <input type="hidden" name="_method" :value="isFavorited ? 'DELETE' : 'POST'">
          <button class="favorite-btn" type="submit">
            <i class='bx' :class="isFavorited ? 'bxs-star' : 'bx-star'"></i>
            <span>FAVORITE</span>
          </button>
        </form>
        <button id="focus-mode-btn" class="focus-mode-btn">
          <i class='bx bx-expand'></i>
          <span>FOCUS MODE</span>
        </button>
      </div>
    </div>

    <div class="under-pdf">
      @livewire('reviews', ['product' => $product])
    </div>
  </section>

  <!-- BOOKMARK SCRIPT -->
  <script>
    let isBookmarked = false;
    let bookmarkedPage = null;

    const bookmarkBtn = document.getElementById('bookmark-btn');
    const bookmarkIcon = bookmarkBtn.querySelector('i');
    const productId = bookmarkBtn.dataset.productId;
    const csrfToken = '{{ csrf_token() }}';

    /**
     * Updates the bookmark button's icon and title based on the isBookmarked state.
     */
    function updateBookmarkButton() {
      if (isBookmarked) {
        bookmarkIcon.classList.remove('bx-bookmark-alt');
        bookmarkIcon.classList.add('bxs-bookmark-alt');
        bookmarkBtn.title = 'Remove bookmark';
      } else {
        bookmarkIcon.classList.remove('bxs-bookmark-alt');
        bookmarkIcon.classList.add('bx-bookmark-alt');
        bookmarkBtn.title = 'Bookmark this page';
      }
    }

    /**
     * Fetches the current bookmark status from the server for this book.
     */
    async function fetchBookmark() {
        const response = await fetch(`/bookmarks/${productId}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            const bookmark = await response.json();
            // If a bookmark exists and has a page number
            if (bookmark && bookmark.page_number) {
                isBookmarked = true;
                bookmarkedPage = bookmark.page_number;
            } else {
                // No bookmark found
                isBookmarked = false;
                bookmarkedPage = null;
            }
        } else {
            isBookmarked = false;
            bookmarkedPage = null;
        }
        updateBookmarkButton();
    }

    /**
     * This function is called by pdfViewer.js *after* the PDF is loaded and ready.
     */
    async function handlePdfLoad() {
      await fetchBookmark(); // Check server for existing bookmark

      // If a bookmark exists, navigate the PDF viewer to that page
      if (isBookmarked && bookmarkedPage && window.goToPage) {
        window.goToPage(bookmarkedPage);
      }
      updateBookmarkButton();
    }

    // Add a click listener to the bookmark button
    bookmarkBtn.addEventListener('click', async () => {
      // Get the current page number from the PDF viewer script
      const currentPage = window.getCurrentPageNum ? window.getCurrentPageNum() : null;

      // If we couldn't get the page number, show error and stop
      if (!currentPage) {
        window.dispatchEvent(new CustomEvent('show-alert', {
          detail: { 
            message: 'Could not determine current page number.', 
            type: 'error' 
          }
        }));
        return;
      }

      // Determine the API endpoint and HTTP method based on whether we are adding or removing
      const url = isBookmarked ? `/bookmarks/${productId}` : '/bookmarks';
      const method = isBookmarked ? 'DELETE' : 'POST';

      const response = await fetch(url, {
        method: method,
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        // Only send body for POST request (when creating/updating bookmark)
        body: method === 'POST' ? JSON.stringify({
          product_id: productId,
          page_number: currentPage
        }) : null
      });

      if (response.ok) {
        const data = await response.json();

        if (data.success) {
          isBookmarked = !isBookmarked; // Toggle the bookmarked state
          bookmarkedPage = isBookmarked ? currentPage : null; // Update stored bookmarked page
          updateBookmarkButton();       // Update the button appearance
          
          window.dispatchEvent(new CustomEvent('show-alert', {
            detail: { 
              message: data.message, 
              type: 'success' 
            }
          }));
        } else {
          window.dispatchEvent(new CustomEvent('show-alert', {
            detail: { 
              message: data.message || 'An unknown error occurred.', 
              type: 'error' 
            }
          }));
        }
      } else {
        window.dispatchEvent(new CustomEvent('show-alert', {
          detail: { 
            message: 'Failed to update bookmark.', 
            type: 'error' 
          }
        }));
      }
    });
  </script>

  <!-- READ BOOK SCRIPT -->
  <script>
    let isRead = false;
    
    const readBtn = document.getElementById('read-btn');
    const readIcon = readBtn.querySelector('i');
    const readBookProductId = readBtn.dataset.productId;
    const readBookCsrfToken = '{{ csrf_token() }}';

    /**
     * Updates the read button's icon and title based on the isRead state.
     */
    function updateReadButton() {
      if (isRead) {
        readIcon.classList.remove('bx-check-circle');
        readIcon.classList.add('bxs-check-circle');
        readBtn.title = 'Mark as unread';
      } else {
        readIcon.classList.remove('bxs-check-circle');
        readIcon.classList.add('bx-check-circle');
        readBtn.title = 'Mark as read';
      }
    }

    /**
     * Fetches the current read status from the server for this book.
     */
    async function fetchReadStatus() {
      const response = await fetch(`/read-books/${readBookProductId}`, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
      });

      if (response.ok) {
        const data = await response.json();
        isRead = data.is_read;
      } else {
        isRead = false;
      }
      
      updateReadButton();
    }

    // Fetch read status when page loads
    document.addEventListener('DOMContentLoaded', function() {
      fetchReadStatus();
    });

    // Add a click listener to the read button
    readBtn.addEventListener('click', async () => {
      const response = await fetch(`/read-books/${readBookProductId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': readBookCsrfToken,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      if (response.ok) {
        const data = await response.json();
        isRead = data.is_read;
        updateReadButton();
        
        window.dispatchEvent(new CustomEvent('show-alert', {
          detail: { 
            message: data.message, 
            type: 'success' 
          }
        }));
      } else {
        window.dispatchEvent(new CustomEvent('show-alert', {
          detail: { 
            message: 'Failed to update read status', 
            type: 'error' 
          }
        }));
      }
    });
  </script>

  <!-- NOTE SCRIPT   -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const productId = {{ $data->id }};
      const noteArea = document.getElementById('note-area');
      const charCount = document.getElementById('char-count');
      let saveTimeout;

      function updateCharCount() {
        const currentLength = noteArea.value.length;
        charCount.textContent = currentLength;

        if (currentLength > 7000) {
          charCount.classList.add('near-limit');
        } else {
          charCount.classList.remove('near-limit');
        }
      }

      updateCharCount();

      // Fetch existing note
      fetch(`/notes/${productId}`)
        .then(response => response.json())
        .then(note => {
          if (note && note.note_text != null) {
            noteArea.value = note.note_text;
          } else {
            noteArea.value = '';
          }
          updateCharCount();
        })
        .catch(() => {
          noteArea.value = '';
          updateCharCount();
        });

      // Handle input changes
      noteArea.addEventListener('input', function() {
        updateCharCount();
        clearTimeout(saveTimeout);

        saveTimeout = setTimeout(function() {
          const noteText = noteArea.value;
          saveOrUpdateNote(noteText, productId);
        }, 500);
      });

      function saveOrUpdateNote(noteText, productId) {
        // Use POST to the store route and send JSON
        fetch(`/notes`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}",
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              note_text: noteText,
              product_id: productId
            })
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            // Silently saved - no need to notify for autosave
          })
          .catch(() => {
            window.dispatchEvent(new CustomEvent('show-alert', {
              detail: {
                message: 'Error saving note. Please try again.',
                type: 'error'
              }
            }));
          });
      }
    });
  </script>

  <!-- Add styles for character counter -->
  <style>
    .notes-name {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .character-counter {
      font-size: 12px;
      color: #999;
      margin-right: 10px;
    }

    .near-limit {
      color: #ff6b6b;
      font-weight: bold;
    }
    
    /* Style for the "Mark as Read" button */
    #read-btn {
      background-color: transparent;
      border: none;
      color: white;
      cursor: pointer;
      transition: transform 0.3s, color 0.3s;
      font-size: 18px;
      padding: 5px 10px;
    }
    
    #read-btn:hover {
      transform: scale(1.2);
      color: #4caf50;
    }
    
    #read-btn .bxs-check-circle {
      color: #4caf50;
    }
  </style>

  <!-- NAVIGATE MOBILE BUTTON -->
  <script>
    const notesButton = document.getElementById('toggle-button');
    const toggleIcon = notesButton.querySelector('.toggle-icon');

    let isAtNotesSection = false;

    notesButton.addEventListener('click', () => {
      const notesSection = document.getElementById('notes-div');

      if (isAtNotesSection) {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
        toggleIcon.classList.replace('bx-book', 'bx-notepad');
      } else {
        notesSection.scrollIntoView({
          behavior: 'smooth'
        });
        toggleIcon.classList.replace('bx-notepad', 'bx-book');
      }

      isAtNotesSection = !isAtNotesSection;
    });
  </script>



  <!-- FOCUS MODE SCRIPT -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const focusModeBtn = document.getElementById('focus-mode-btn');
      const exitFocusModeBtn = document.getElementById('exit-focus-mode');
      const body = document.body;

      focusModeBtn.addEventListener('click', function() {
        body.classList.add('focus-mode');
        exitFocusModeBtn.style.display = 'flex';
        // Store scroll position
        sessionStorage.setItem('scrollPos', window.pageYOffset);
        // Scroll to top
        window.scrollTo(0, 0);
      });

      exitFocusModeBtn.addEventListener('click', function() {
        body.classList.remove('focus-mode');
        exitFocusModeBtn.style.display = 'none';
        // Restore scroll position
        const scrollPos = sessionStorage.getItem('scrollPos');
        if (scrollPos) {
          window.scrollTo(0, parseInt(scrollPos));
          sessionStorage.removeItem('scrollPos');
        }
      });

      // Add keyboard shortcut (Esc) to exit focus mode
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && body.classList.contains('focus-mode')) {
          exitFocusModeBtn.click();
        }
      });
    });
  </script>

  <!-- Listen for Livewire events -->
  <script>
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('alert', (data) => {
        window.dispatchEvent(new CustomEvent('show-alert', {
          detail: {
            message: data.message,
            type: data.type
          }
        }));
      });
    });
  </script>

  @livewireScripts
</body>

</html>
