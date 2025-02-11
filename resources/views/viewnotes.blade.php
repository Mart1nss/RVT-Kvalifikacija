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
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>

  @include('components.alert')
  @include('navbar')

  <div class="main-container" x-data="{
      sortType: 'newest',
      sortDropdownOpen: false,
      notes: [],
      init() {
          this.notes = Array.from(document.querySelectorAll('.item-card')).map(card => ({
              element: card,
              timestamp: card.querySelector('.last-updated').dataset.timestamp
          }));
          this.updateTimes();
          setInterval(() => this.updateTimes(), 60000);
      },
      toggleSort(type) {
          this.sortType = type;
          this.sortDropdownOpen = false;
          this.sortNotes();
      },
      sortNotes() {
          const container = document.querySelector('.item-container');
          this.notes.sort((a, b) => {
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
      }
  }">

    <div class="text-container">
      <h1 class="text-container-title">Notes</h1>
      <div class="sort-dropdown" @click.outside="sortDropdownOpen = false">
        <button class="dropdown-btn" @click="sortDropdownOpen = !sortDropdownOpen">
          <i class='bx bx-sort-alt-2'></i>
          <span x-text="sortType === 'newest' ? 'Newest First' : 'Oldest First'"></span>
        </button>
        <ul class="dropdown-content" :class="{ 'show': sortDropdownOpen }">
          <li @click="toggleSort('newest')" :class="{ 'selected': sortType === 'newest' }">Newest First</li>
          <li @click="toggleSort('oldest')" :class="{ 'selected': sortType === 'oldest' }">Oldest First</li>
        </ul>
      </div>
    </div>

    @if ($notes->count() > 0)
      <div class="item-container">
        @foreach ($notes as $note)
          <div class="item-card" x-data="{ expanded: false }">
            <div class="item-card-header" :class="{ 'active': expanded }">
              <div class="div-control" @click="expanded = !expanded">
                <div class="note-header">
                  <b style="font-size:18px">{{ $note->product->title }}</b> by <b
                    style="font-size:18px">{{ $note->product->author }} </b><br>
                  <span class="last-updated" data-timestamp="{{ $note->updated_at->timestamp }}">
                    {{-- dynamic text --}}
                  </span>
                </div>

                <div class="note-btn">
                  <i class='bx bxs-down-arrow-alt accordion-icon'
                    :style="{ transform: expanded ? 'rotate(180deg)' : 'rotate(0deg)' }"></i>
                  <a href="{{ route('view', ['id' => $note->product_id]) }}" class="goto-note-btn" @click.stop>
                    <i class='bx bxs-arrow-to-right'></i>
                  </a>
                </div>
              </div>
            </div>
            <div class="note-content" x-show="expanded" x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 transform -translate-y-2"
              x-transition:enter-end="opacity-100 transform translate-y-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 transform translate-y-0"
              x-transition:leave-end="opacity-0 transform -translate-y-2">
              {{ $note->note_text }}
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="item-container">
        <div class="text-container">
          <h1>Notes</h1>
          <div class="sort-dropdown">
            <button class="sort-button">
              Sort by <i class='bx bx-sort-alt-2'></i>
            </button>
            <div class="sort-options">
              <a href="#" data-sort="newest" class="active">Newest First</a>
              <a href="#" data-sort="oldest">Oldest First</a>
            </div>
          </div>
        </div>
        <p style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">
          You don't have any notes yet.</p>
      </div>
    @endif

  </div>

  </div>

  </div>

</body>

</html>
