<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>View Book</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link type="text/css"  rel="stylesheet" href="{{ asset('css/pdf-view.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('pdfjs-express/lib/ui/style.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

</head>

<body>
  @include('navbar')


  <div class="error-div">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    </div>

    <div class="container" style="margin-bottom: 10px;">
      <div class="pdf-viewer-wrapper">
        <div id="pdf-toolbar">
          <button id="zoom-in"><i class='bx bx-zoom-in'></i></button>
          <button id="zoom-out"><i class='bx bx-zoom-out' ></i></button>
          <span>Page: <span id="page-num">1</span> / <span id="page-count"></span></span>
          <form id="bookmark-form" action="{{ $product->isFavoritedBy(auth()->user()) ? route('favorites.delete', $product->id) : route('favorites.add', $product->id) }}" method="POST" style="display: inline;">
            @csrf
            @if($product->isFavoritedBy(auth()->user()))
                @method('DELETE') 
            @endif
            <button id="bookmark-btn" class="btn {{ $product->isFavoritedBy(auth()->user()) ? '' : 'btn-primary' }}" type="submit" data-bookmarked="{{ $product->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}">
              <i class='bx {{ $product->isFavoritedBy(auth()->user()) ? "bxs-bookmark" : "bx-bookmark" }}'></i>
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
    <script>
      //BOOKMARK FUNCTIONALITY
        document.addEventListener('DOMContentLoaded', function () {
            const pdfUrl = "/assets/{{ $data->file }}";
            initPDFViewer(pdfUrl);

            // Bookmark form functionality
            const bookmarkForm = document.getElementById('bookmark-form');
            const bookmarkBtn = document.getElementById('bookmark-btn');
            const bookmarkPageSpan = document.getElementById('bookmark-page');
            
            // Function to update bookmark page display
            function updateBookmarkPageDisplay(pageNumber = null) {
                if (pageNumber) {
                    bookmarkPageSpan.textContent = `P${pageNumber}`;
                } else {
                    bookmarkPageSpan.textContent = '';
                }
            }

            // Load initial bookmark state
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

      <button id="toggle-button" class="fab">
        <i class="toggle-icon bx bx-notepad"></i>  
      </button>

      <div id="notes-div" class="notes-div">
        <div class="notes-name">
          <p class="notes-p">notes</p>
        </div>
        <textarea id="note-area"  placeholder="Type anything here...">
        </textarea>
      </div>

  </div>

  <section id="underPdf" class="container-under-pdf-view">

    <div class="metadata">
      <h2>{{$data->title}} by {{$data->author}}</h2>
      <form class="favorite-form" action="{{ $product->isFavoritedBy(auth()->user()) ? route('favorites.delete', $product->id) : route('favorites.add', $product->id) }}" method="POST">
        @csrf
        @if($product->isFavoritedBy(auth()->user()))
            @method('DELETE') 
        @endif
        <button class="favorite-btn" style="padding: 5px 30px; margin-left: 10px;" type="submit">
            <i class='bx bx-star @if($product->isFavoritedBy(auth()->user())) bx bxs-star @endif'></i> FAVORITE
        </button>
    </form>
    
    </div>

    <div class="under-pdf">

      <div id="reviews-div" class="reviews-div">
        <h1 class="reviews-header">REVIEWS</h1>
        @auth
            <form class="review-form" method="POST" action="{{ route('products.reviews.store', $product->id) }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="rating-container">
                    <label class="review-label" for="review_score"></label>
                    <div class="star-rating">
                        @for ($i = 5; $i >= 1; $i--) 
                            <input style="display: none;" type="radio" id="rating-{{ $i }}" name="review_score" value="{{ $i }}" required>
                            <label for="rating-{{ $i }}" class="star" style="font-size: 32px;">&#9734;</label>
                        @endfor
                    </div>
                </div>
                <div class="review-input-button">
                <textarea class="review-textbox" name="review_text" placeholder="Write your review here..." required></textarea>
                <button class="button-review" type="submit">Submit</button>
                </div>
            </form>
        @else
            <p><a href="{{ route('login') }}">Login</a> to add a review.</p>
        @endauth
            <h3 style="margin-bottom: 8px; font-family: sans-serif; font-weight: 600; text-transform: uppercase;">Sort by</h3>
        <div class="filter-buttons">
          <button id="sort-highest" class="button-filter" onclick="sortReviews('highest')">Highest Rated</button>
          <button id="sort-lowest" class="button-filter" onclick="sortReviews('lowest')">Lowest Rated</button>
      </div>
      <div id="reviews-container">
        @foreach($reviews as $review)
            <div class="review-card" data-score="{{ $review->review_score }}">
              <p class="reviewed-by"> {{ $review->user->name }}</p>
                <div class="star-rating">
                    @for ($i = 1; $i <= $review->review_score; $i++)
                        <span class="star filled" style="color: gold;">&#9733;</span> 
                    @endfor
                    @for ($i = $review->review_score + 1; $i <= 5; $i++)
                        <span class="star">&#9734;</span> 
                    @endfor
                </div>
                <p style="margin-bottom: 10px;">{{ $review->review_text }}</p>
                <span class="last-updated" data-timestamp="{{ $review->updated_at->timestamp }}">
                  {{-- dynamic text  --}}
              </span>
                @if (auth()->check() && auth()->id() === $review->user_id)
                    <form method="POST" action="{{ route('products.reviews.destroy', [$product->id, $review->id]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-delete">Delete</button>
                    </form>
                @endif
            </div>
        @endforeach
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
    function sortReviews(order) {
        let highestButton = document.getElementById('sort-highest');
        let lowestButton = document.getElementById('sort-lowest');
        let reviewsContainer = document.getElementById('reviews-container');
        let reviews = Array.from(reviewsContainer.getElementsByClassName('review-card'));

        if (order === 'highest') {
            if (highestButton.classList.contains('active')) {
                highestButton.classList.remove('active');
                sortReviews('none');
            } else {
                highestButton.classList.add('active');
                lowestButton.classList.remove('active');
                reviews.sort((a, b) => b.dataset.score - a.dataset.score);
            }
        } else if (order === 'lowest') {
            if (lowestButton.classList.contains('active')) {
                lowestButton.classList.remove('active');
                sortReviews('none');
            } else {
                lowestButton.classList.add('active');
                highestButton.classList.remove('active');
                reviews.sort((a, b) => a.dataset.score - b.dataset.score);
            }
        } else {
            reviews.sort((a, b) => b.dataset.timestamp - a.dataset.timestamp); 
        }

        reviews.forEach(review => reviewsContainer.appendChild(review));
    }

    document.addEventListener('DOMContentLoaded', function () {
        let reviewsContainer = document.getElementById('reviews-container');
        let reviews = Array.from(reviewsContainer.getElementsByClassName('review-card'));

        reviews.forEach(review => {
            review.dataset.timestamp = new Date(review.querySelector('.reviewed-by').dataset.timestamp).getTime();
        });
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
          window.scrollTo({ top: 0, behavior: 'smooth' });
          toggleIcon.classList.replace('bx-book', 'bx-notepad');
      } else {
          notesSection.scrollIntoView({ behavior: 'smooth' });
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
  

</body>
</html>