<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>View Book</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link type="text/css" rel="stylesheet" href="{{ asset('css/pdf-view.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

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
        <form id="bookmark-form"
          action="{{ $product->isFavoritedBy(auth()->user()) ? route('favorites.delete', $product->id) : route('favorites.add', $product->id) }}"
          method="POST" style="display: inline;">
          @csrf
          @if ($product->isFavoritedBy(auth()->user()))
            @method('DELETE')
          @endif
          <button id="bookmark-btn" class="btn {{ $product->isFavoritedBy(auth()->user()) ? '' : 'btn-primary' }}"
            type="submit" data-bookmarked="{{ $product->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}">
            <i class='bx {{ $product->isFavoritedBy(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
          </button>
          <span id="bookmark-page" style="display: inline-block; margin-left: 4px; font-size: 0.8em;"></span>
        </form>
      </div>

      <div class="container">

        <div id="pdf-container"></div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
    <script src="{{ asset('js/pdfViewer.js') }}"></script>


    <button id="toggle-button" class="fab">
      <i class="toggle-icon bx bx-notepad"></i>
    </button>

    <div id="notes-div" class="notes-div">
      <div class="notes-name">
        <p class="notes-p">notes</p>
      </div>
      <textarea id="note-area" placeholder="Type anything here...">
        </textarea>
    </div>

  </div>

  <section id="underPdf" class="container-under-pdf-view">

    <div class="metadata">
      <h2>{{ $data->title }} by {{ $data->author }}</h2>
      <div class="action-buttons">
        <form class="favorite-form"
          action="{{ $product->isFavoritedBy(auth()->user()) ? route('favorites.delete', $product->id) : route('favorites.add', $product->id) }}"
          method="POST">
          @csrf
          @if ($product->isFavoritedBy(auth()->user()))
            @method('DELETE')
          @endif
          <button class="favorite-btn" type="submit">
            <i class='bx bx-star @if ($product->isFavoritedBy(auth()->user())) bx bxs-star @endif'></i>
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

      <div id="reviews-div" class="reviews-div">
        <h1 class="reviews-header">REVIEWS</h1>
        @auth
          <form class="review-form" method="POST" action="{{ route('products.reviews.store', $product->id) }}"
            id="reviewForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="rating-container">
              <div class="star-rating-header">
                <label class="review-label">Rating</label>
                <span class="rating-error" id="ratingError">Please select a rating</span>
              </div>
              <div class="star-rating">
                @for ($i = 5; $i >= 1; $i--)
                  <input type="radio" id="rating-{{ $i }}" name="review_score" value="{{ $i }}">
                  <label for="rating-{{ $i }}" class="star">
                    <i class='bx bxs-star'></i>
                  </label>
                @endfor
              </div>
            </div>
            <div class="review-input-button">
              <textarea class="review-textbox" name="review_text" placeholder="Write your review here..." required maxlength="250"
                oninput="updateCharCount(this, 'reviewCharCount', 250)"></textarea>
              <div class="char-count" id="reviewCharCount">0 / 250</div>
              <button class="button-review" type="submit">Submit</button>
            </div>
          </form>
        @else
          <p><a href="{{ route('login') }}">Login</a> to add a review.</p>
        @endauth
        <h3
          style="margin-bottom: 8px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 18px;">
          Sort by
        </h3>
        <div class="filter-buttons">
          <div class="dropdown">
            <button id="sortButton" class="sort-dropdown" onclick="toggleDropdown(event, 'sortOptions')">
              <i class='bx bx-sort-alt-2'></i>
              <span id="currentSort">Default</span>
            </button>
            <div id="sortOptions" class="dropdown-content">
              <a href="#" onclick="handleSort('default', event)">Default</a>
              <a href="#" onclick="handleSort('highest', event)">Highest Rated</a>
              <a href="#" onclick="handleSort('lowest', event)">Lowest Rated</a>
              <a href="#" onclick="handleSort('newest', event)">Newest Reviews</a>
            </div>
          </div>
        </div>
        <div id="reviews-container">
          @if ($reviews->isEmpty())
            <div class="no-reviews">
              <i class='bx bx-message-square-dots'></i>
              <p>There are no reviews yet.</p>
              <p>Be the first to share your thoughts!</p>
            </div>
          @else
            @foreach ($reviews as $review)
              <div class="review-card" data-score="{{ $review->review_score }}">
                @if (auth()->check() && (auth()->id() === $review->user_id || auth()->user()->isAdmin()))
                  <div class="review-options">
                    <button class="review-options-btn" onclick="toggleReviewOptions(event, {{ $review->id }})">
                      <i class='bx bx-dots-vertical-rounded'></i>
                    </button>
                    <div id="reviewOptions{{ $review->id }}" class="review-options-dropdown">
                      <form method="POST"
                        action="{{ route('products.reviews.destroy', [$product->id, $review->id]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit">
                          <i class='bx bx-trash'></i>
                          Delete{{ auth()->user()->isAdmin() && auth()->id() !== $review->user_id ? ' (Admin)' : '' }}
                        </button>
                      </form>
                    </div>
                  </div>
                @endif
                <p class="reviewed-by">{{ $review->user->name }}</p>
                <div class="star-rating">
                  @for ($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= $review->review_score ? 'filled' : '' }}">
                      <i class='bx bxs-star'></i>
                    </span>
                  @endfor
                </div>
                <p style="margin-bottom: 10px;">{{ $review->review_text }}</p>
                <span class="last-updated" data-timestamp="{{ $review->updated_at->timestamp }}">
                  {{-- dynamic text --}}
                </span>
              </div>
            @endforeach
          @endif
        </div>
      </div>





    </div>

  </section>

  <script>
    function updateTimeDisplays() {
      $('.last-updated').each(function() {
        const timestamp = $(this).data('timestamp') * 1000;
        const timeAgoText = moment(timestamp).fromNow();
        $(this).html('<em>' + timeAgoText + '</em>');
      });
    }

    updateTimeDisplays();

    setInterval(updateTimeDisplays, 60000);
  </script>

  <script>
    //NOTIFICATION FADE OUT SCRIPT
    function fadeOutAndRemove(element) {
      element.classList.add('fade-out');
      setTimeout(function() {
        element.remove();
      }, 1500);
    }

    window.addEventListener('load', function() {
      var alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        setTimeout(function() {
          fadeOutAndRemove(alert);
        }, 1500);
      });
    });
  </script>




  <!-- SORT REVIEWS -->
  <script>
    function toggleDropdown(event, dropdownId) {
      event.stopPropagation();
      var dropdownContent = document.getElementById(dropdownId);
      var currentText = document.getElementById("currentSort").textContent;

      dropdownContent.classList.toggle("show");

      if (dropdownContent.classList.contains("show")) {
        var options = dropdownContent.getElementsByTagName("a");
        for (var i = 0; i < options.length; i++) {
          options[i].classList.remove("active");
          if (options[i].textContent === currentText) {
            options[i].classList.add("active");
          }
        }
      }
    }

    function handleSort(order, event) {
      event.preventDefault();
      event.stopPropagation();

      let currentSortText = document.getElementById("currentSort");
      let dropdownContent = document.getElementById("sortOptions");
      let reviewsContainer = document.getElementById('reviews-container');
      let reviews = Array.from(reviewsContainer.getElementsByClassName('review-card'));

      // Update button text based on selection
      switch (order) {
        case 'highest':
          currentSortText.textContent = "Highest Rated";
          reviews.sort((a, b) => b.dataset.score - a.dataset.score);
          break;
        case 'lowest':
          currentSortText.textContent = "Lowest Rated";
          reviews.sort((a, b) => a.dataset.score - b.dataset.score);
          break;
        case 'newest':
          currentSortText.textContent = "Newest Reviews";
          reviews.sort((a, b) => {
            const timestampA = new Date(a.querySelector('.last-updated').dataset.timestamp * 1000);
            const timestampB = new Date(b.querySelector('.last-updated').dataset.timestamp * 1000);
            return timestampB - timestampA;
          });
          break;
        default:
          currentSortText.textContent = "Default";
          reviews.sort((a, b) => {
            const timestampA = new Date(a.querySelector('.last-updated').dataset.timestamp * 1000);
            const timestampB = new Date(b.querySelector('.last-updated').dataset.timestamp * 1000);
            return timestampA - timestampB;
          });
      }

      // Close dropdown after selection
      dropdownContent.classList.remove("show");

      // Clear the container and append sorted reviews
      while (reviewsContainer.firstChild) {
        reviewsContainer.removeChild(reviewsContainer.firstChild);
      }
      reviews.forEach(review => reviewsContainer.appendChild(review));
    }

    // Close dropdowns if user clicks anywhere on the page
    document.addEventListener('click', function(event) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var sortButton = document.getElementById("sortButton");

      for (var i = 0; i < dropdowns.length; i++) {
        var dropdown = dropdowns[i];
        if (!sortButton.contains(event.target) && !dropdown.contains(event.target)) {
          dropdown.classList.remove("show");
        }
      }
    });
  </script>


  <!-- NAVIGATE BUTTON -->
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

  <!-- NOTE SCRIPT   -->
  <script>
    $(document).ready(function() {
      const productId = {{ $data->id }};
      let saveTimeout;

      $.get(`/notes/${productId}`, function(note) {
        if (note) {
          $('#note-area').val(note.note_text);
        }
      });

      $('#note-area').on('input', function() {
        clearTimeout(saveTimeout);

        saveTimeout = setTimeout(function() {
          const noteText = $('#note-area').val();
          saveOrUpdateNote(noteText, productId);
        }, 1000);
      });

      function saveOrUpdateNote(noteText, productId) {
        $.ajax({
          url: `/notes/${productId}`,
          method: 'PUT',
          data: {
            note_text: noteText,
            product_id: productId,
            _token: "{{ csrf_token() }}"
          },
          success: function(response) {
            console.log(response.message);
          }
        });
      }
    });
  </script>

  <script>
    function toggleReviewOptions(event, reviewId) {
      event.stopPropagation();
      const dropdown = document.getElementById(`reviewOptions${reviewId}`);
      const allDropdowns = document.querySelectorAll('.review-options-dropdown');

      // Close all other dropdowns
      allDropdowns.forEach(d => {
        if (d !== dropdown && d.classList.contains('show')) {
          d.classList.remove('show');
        }
      });

      dropdown.classList.toggle('show');
    }

    // Close review options when clicking outside
    document.addEventListener('click', function(event) {
      const dropdowns = document.querySelectorAll('.review-options-dropdown');
      dropdowns.forEach(dropdown => {
        if (!event.target.closest('.review-options')) {
          dropdown.classList.remove('show');
        }
      });
    });
  </script>

  <script>
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent form submission by default

      const ratingInputs = document.querySelectorAll('input[name="review_score"]');
      const ratingError = document.getElementById('ratingError');
      let isRatingSelected = false;

      ratingInputs.forEach(input => {
        if (input.checked) {
          isRatingSelected = true;
        }
      });

      if (!isRatingSelected) {
        ratingError.classList.add('show');

        // Hide the error message after 3 seconds
        setTimeout(() => {
          ratingError.classList.remove('show');
        }, 3000);
        return; // Stop form submission
      }

      // If rating is selected, submit the form
      this.submit();
    });

    // Hide error when rating is selected
    document.querySelectorAll('input[name="review_score"]').forEach(input => {
      input.addEventListener('change', () => {
        document.getElementById('ratingError').classList.remove('show');
      });
    });
  </script>

  <script>
    function updateCharCount(input, counterId, limit) {
      const counter = document.getElementById(counterId);
      const currentLength = input.value.length;
      counter.textContent = `${currentLength} / ${limit}`;

      if (currentLength >= limit) {
        counter.style.color = '#dc3545';
      } else {
        counter.style.color = '#aaa';
      }
    }

    // Initialize counter when page loads
    document.addEventListener('DOMContentLoaded', function() {
      const textarea = document.querySelector('.review-textbox');
      if (textarea) {
        updateCharCount(textarea, 'reviewCharCount', 250);
      }
    });
  </script>

  <script>
    function adjustTextareaHeight(textarea) {
      textarea.style.height = "40px";
      textarea.style.height = textarea.scrollHeight + "px";
    }

    const textareas = document.querySelectorAll(".review-textbox");

    textareas.forEach(textarea => {
      adjustTextareaHeight(textarea);

      textarea.addEventListener("input", function() {
        adjustTextareaHeight(this);
      });
    });
  </script>

  <script>
    //BOOKMARK FUNCTIONALITY
    document.addEventListener('DOMContentLoaded', function() {
      const pdfUrl = "/assets/{{ $data->file }}";
      initPDFViewer(pdfUrl);

      const bookmarkForm = document.getElementById('bookmark-form');
      const bookmarkBtn = document.getElementById('bookmark-btn');
      const bookmarkPageSpan = document.getElementById('bookmark-page');

      function updateBookmarkPageDisplay(pageNumber = null) {
        if (pageNumber) {
          bookmarkPageSpan.textContent = `P${pageNumber}`;
        } else {
          bookmarkPageSpan.textContent = '';
        }
      }

      fetch(`/bookmarks/{{ $product->id }}`)
        .then(response => response.json())
        .then(data => {
          if (data && data.id) {
            updateBookmarkPageDisplay(data.page_number);
          }
        })
        .catch(error => console.error('Error:', error));

      bookmarkForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const currentPage = document.getElementById('page-num').textContent;

        fetch(this.action, {
            method: this.method,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: new FormData(this)
          })
          .then(response => {
            if (response.ok) {
              const isBookmarked = bookmarkBtn.getAttribute('data-bookmarked') === 'true';
              const bookmarkIcon = bookmarkBtn.querySelector('i');

              if (isBookmarked) {
                bookmarkIcon.className = 'bx bx-bookmark';
                bookmarkBtn.setAttribute('data-bookmarked', 'false');
                bookmarkBtn.classList.add('btn-primary');
                bookmarkForm.action = '{{ route('favorites.add', $product->id) }}';
                bookmarkForm.querySelector('input[name="_method"]')?.remove();
                updateBookmarkPageDisplay(null);
              } else {
                bookmarkIcon.className = 'bx bxs-bookmark';
                bookmarkBtn.setAttribute('data-bookmarked', 'true');
                bookmarkBtn.classList.remove('btn-primary');
                bookmarkForm.action = '{{ route('favorites.delete', $product->id) }}';
                updateBookmarkPageDisplay(currentPage);
                if (!bookmarkForm.querySelector('input[name="_method"]')) {
                  const methodInput = document.createElement('input');
                  methodInput.type = 'hidden';
                  methodInput.name = '_method';
                  methodInput.value = 'DELETE';
                  bookmarkForm.appendChild(methodInput);
                }
              }
            }
          })
          .catch(error => console.error('Error:', error));
      });
    });
  </script>

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

</body>

</html>
