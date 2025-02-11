<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>View Book</title>
  <link type="text/css" rel="stylesheet" href="{{ asset('css/pdf-view.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <!-- Add Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Add jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        const pdfUrl = "/assets/{{ $data->file }}";
        initPDFViewer(pdfUrl);
      });
    </script>

    <button id="toggle-button" class="fab">
      <i class="toggle-icon bx bx-notepad"></i>
    </button>

    <div id="notes-div" class="notes-div">
      <div class="notes-name">
        <p class="notes-p">notes</p>
      </div>
      <textarea id="note-area" placeholder="Type anything here..."></textarea>
    </div>
  </div>

  <section id="underPdf" class="container-under-pdf-view">
    <div class="metadata">
      <h2>{{ $data->title }} by {{ $data->author }}</h2>
      <div class="action-buttons">
        <form class="favorite-form"
          action="{{ $product->isFavoritedBy(auth()->user()) ? route('my-collection.delete', $product->id) : route('my-collection.add', $product->id) }}"
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
      @include('components.reviews-section', ['product' => $product, 'reviews' => $product->reviews])
    </div>
  </section>

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
</body>

</html>
