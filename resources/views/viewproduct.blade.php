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
        // Initialize viewer and pass callback for post-load actions
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
     * Updates the isBookmarked and bookmarkedPage variables.
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
            // Failed to fetch status (server error, network issue, etc.)
            console.error('Failed to fetch bookmark status:', response.status, response.statusText);
            isBookmarked = false;
            bookmarkedPage = null;
            // Optionally show an alert to the user
            // window.showAlert('Could not load bookmark status.', 'error');
        }
        // Update the button appearance after checking
        updateBookmarkButton();
    }

    /**
     * This function is called by pdfViewer.js *after* the PDF is loaded and ready.
     * It fetches the bookmark and navigates to it if found.
     */
    async function handlePdfLoad() {
      await fetchBookmark(); // Check server for existing bookmark

      // If a bookmark exists, navigate the PDF viewer to that page
      if (isBookmarked && bookmarkedPage && window.goToPage) {
        window.goToPage(bookmarkedPage);
      } else {
      }
      updateBookmarkButton();
    }

    // Add a click listener to the bookmark button
    bookmarkBtn.addEventListener('click', async () => {
      // Get the current page number from the PDF viewer script
      const currentPage = window.getCurrentPageNum ? window.getCurrentPageNum() : null;

      // If we couldn't get the page number, show error and stop
      if (!currentPage) {
        console.error("Could not get current page number for bookmarking.");
        window.showAlert('Could not determine current page number.', 'error');
        return;
      }

      // Determine the API endpoint and HTTP method based on whether we are adding or removing
      const url = isBookmarked ? `/bookmarks/${productId}` : '/bookmarks';
      const method = isBookmarked ? 'DELETE' : 'POST';

      try {
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

        // Check if the request was successful
        if (!response.ok) {
          // Try to get error message from server response, or use a default
          let errorMsg = 'Failed to update bookmark.';
          try {
            const errorData = await response.json();
            errorMsg = errorData.message || errorMsg;
          } catch (e) { /* Ignore if response is not JSON */ }
          throw new Error(errorMsg); // Throw error to be caught below
        }

        // Request was successful, parse the JSON response
        const data = await response.json();

        // Double-check the success flag from our API response
        if (data.success) {
          isBookmarked = !isBookmarked; // Toggle the bookmarked state
          bookmarkedPage = isBookmarked ? currentPage : null; // Update stored bookmarked page
          updateBookmarkButton();       // Update the button appearance
          window.showAlert(data.message, 'success'); // Show success message
        } else {
          // API reported failure even with OK status (should ideally not happen)
          throw new Error(data.message || 'An unknown error occurred.');
        }

      } catch (error) {
        console.error('Error saving/removing bookmark:', error);
        window.showAlert(error.message || 'An error occurred while managing bookmark.', 'error');
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
        .catch(error => console.error('Error loading note:', error));

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
            console.log(data.message);
          })
          .catch(error => {
            console.error('Error saving note:', error);
            // Show error aler
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
