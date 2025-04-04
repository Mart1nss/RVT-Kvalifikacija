<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Notes</title>
  <link rel="stylesheet" href="{{ asset('css/notes-style.css') }}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>

  @include('components.alert')
  @include('navbar')

  <div class="main-container" x-data="{
      sortType: 'newest',
      sortDropdownOpen: false,
      notes: [],
      searchQuery: '',
      searchTimeout: null,
      copyStatus: {},
      errorStatus: {}, // Add error status
      init() {
          this.notes = Array.from(document.querySelectorAll('.item-card')).map(card => ({
              element: card,
              timestamp: card.querySelector('.last-updated').dataset.timestamp,
              title: card.querySelector('.note-title h2').textContent.toLowerCase(),
              author: card.querySelector('.note-author').textContent.toLowerCase()
          }));
          this.updateTimes();
          setInterval(() => this.updateTimes(), 60000);
      },
      toggleSort(type) {
          this.sortType = type;
          this.sortDropdownOpen = false;
          this.sortAndFilterNotes();
      },
      filterNotes() {
          clearTimeout(this.searchTimeout);
          this.searchTimeout = setTimeout(() => {
              this.sortAndFilterNotes();
          }, 300);
      },
      sortAndFilterNotes() {
          const container = document.querySelector('.item-container');
          const query = this.searchQuery.toLowerCase();
  
          this.notes.forEach(note => {
              const matches = !query ||
                  note.title.includes(query) ||
                  note.author.includes(query);
              note.element.style.display = matches ? '' : 'none';
          });
  
          const visibleNotes = this.notes.filter(note =>
              note.element.style.display !== 'none'
          );
  
          visibleNotes.sort((a, b) => {
              return this.sortType === 'newest' ? b.timestamp - a.timestamp : a.timestamp - b.timestamp;
          }).forEach(note => {
              container.appendChild(note.element);
          });
      },
      updateTimes() {
          document.querySelectorAll('.last-updated').forEach(el => {
              const timestamp = el.dataset.timestamp * 1000;
              el.innerHTML = 'Last updated <em>' + moment(timestamp).fromNow() + '</em>';
          });
      },
      copyToClipboard(text, noteId) {
          if (navigator.clipboard && navigator.clipboard.writeText) {
              navigator.clipboard.writeText(text)
                  .then(() => {
                      this.copyStatus[noteId] = true;
                      this.errorStatus[noteId] = false; // Clear any previous error
                      setTimeout(() => {
                          this.copyStatus[noteId] = false;
                      }, 5000);
                  })
                  .catch(err => {
                      console.error('Failed to copy: ', err);
                      this.errorStatus[noteId] = true; // Set error status
                      setTimeout(() => { this.errorStatus[noteId] = false; }, 5000);
                  });
          } else {
              // Fallback for browsers that don't support navigator.clipboard
              console.error('Clipboard API not available');
              this.errorStatus[noteId] = true;
              setTimeout(() => { this.errorStatus[noteId] = false; }, 5000);
          }
      }
  }">

    <div class="text-container">
      <h1 class="text-container-title">Notes</h1>
    </div>

    <div class="search-filter-container" x-data="{ searchQuery: '' }">
      <div class="search-container">
        <input type="text" placeholder="Search by book title or author..." x-model="searchQuery"
          @input="filterNotes()">
      </div>
      <div class="sort-dropdown" @click.outside="sortDropdownOpen = false">
        <button class="btn btn-filter btn-sm" @click="sortDropdownOpen = !sortDropdownOpen">
          <i class='bx bx-sort-alt-2'></i>
          <span x-text="sortType === 'newest' ? 'Newest' : 'Oldest'"></span>
        </button>
        <ul class="dropdown-content" :class="{ 'show': sortDropdownOpen }">
          <li @click="toggleSort('newest')" :class="{ 'selected': sortType === 'newest' }">Newest</li>
          <li @click="toggleSort('oldest')" :class="{ 'selected': sortType === 'oldest' }">Oldest</li>
        </ul>
      </div>
    </div>



    @if ($notes->count() > 0)
      <div class="item-container">
        @foreach ($notes as $note)
          <div class="item-card" x-data="{ expanded: false }"
            @expand-accordion.window="if($event.detail.id !== '{{ $note->id }}') expanded = false">
            <div class="item-card-header"
              @click="
                               if (!expanded) {
                                 $dispatch('expand-accordion', { id: '{{ $note->id }}' });
                               }
                               expanded = !expanded;
                             ">
              <div class="note-title">
                <h2>
                  @if ($note->product)
                    {{ $note->product->title }}
                  @else
                    {{ $note->book_title }} (Deleted)
                  @endif
                </h2>
                <p class="note-author">by @if ($note->product)
                    {{ $note->product->author }}
                  @else
                    {{ $note->book_author }}
                  @endif
                </p>
                <span class="last-updated" data-timestamp="{{ $note->updated_at->timestamp }}">
                  {{-- dynamic text --}}
                </span>
              </div>
              <div class="note-actions">
                @if ($note->product)
                  <a href="{{ route('view', ['id' => $note->product_id]) }}" class="goto-note-btn" @click.stop>
                    <i class='bx bx-book-open'></i>
                  </a>
                @endif
                <i class='bx bx-chevron-down accordion-icon' :class="{ 'rotate': expanded }"></i>
              </div>
            </div>
            <div class="note-content" x-show="expanded" x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 transform -translate-y-2"
              x-transition:enter-end="opacity-100 transform translate-y-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 transform translate-y-0"
              x-transition:leave-end="opacity-0 transform -translate-y-2">
              <div class="note-content-header">
                <p x-ref="noteText{{ $note->id }}">{{ $note->note_text }}</p>
                <button class="copy-btn"
                  @click.stop="copyToClipboard($refs.noteText{{ $note->id }}.innerText, '{{ $note->id }}')"
                  :class="{ 'copied': copyStatus['{{ $note->id }}'], 'error': errorStatus['{{ $note->id }}'] }">
                  <i class='bx bx-copy'></i>
                  <span
                    x-text="copyStatus['{{ $note->id }}'] ? 'Copied!' : (errorStatus['{{ $note->id }}'] ? 'Error' :  'Copy')"></span>
                </button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="item-container">
        <p style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">
          You don't have any notes yet.</p>
      </div>
    @endif
  </div>

  </div>

  </div>


</body>

</html>
