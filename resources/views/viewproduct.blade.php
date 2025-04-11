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
        initPDFViewer(pdfUrl);
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
