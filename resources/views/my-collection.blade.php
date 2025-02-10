<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Favorites</title>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/favorites-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  @include('components.alert')
  @include('navbar')


  <div class="main-container" x-data="{
      activeTab: '{{ old('tab') ?? (request('tab') ?? 'favorites') }}',
      switchTab(tab) {
          // Close any open modals first
          document.querySelectorAll('.mobile-modal').forEach(modal => {
              const modalComponent = Alpine.$data(modal);
              if (modalComponent && modalComponent.isOpen) {
                  modalComponent.close();
              }
          });
  
          // Wait for modal closing animation to complete before switching tabs
          setTimeout(() => {
              this.activeTab = tab;
              updateUrl(tab);
          }, 300); // Match this with your modal closing animation duration
      }
  }">
    <div class="text-container">
      <h1 class="text-container-title">MY COLLECTION</h1>
    </div>

    <div class="tabs-container">
      <button class="tab-btn" :class="{ 'active': activeTab === 'favorites' }" @click="switchTab('favorites')">
        <i class='bx bxs-star'></i> Favorites
      </button>
      <button class="tab-btn" :class="{ 'active': activeTab === 'readlater' }" @click="switchTab('readlater')">
        <i class='bx bxs-bookmark'></i> Read Later
      </button>
    </div>

    <div id="favorites-tab" class="tab-content" :class="{ 'active': activeTab === 'favorites' }">
      <div class="item-container">
        @if ($favorites->count() > 0)
          @foreach ($favorites as $favorite)
            <x-book-card :book="$favorite->product" :source="'favorites'" />
          @endforeach
        @else
          <p
            style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">
            There are no favorites in your collection!</p>
        @endif
      </div>
    </div>

    <div id="readlater-tab" class="tab-content" :class="{ 'active': activeTab === 'readlater' }">
      <div class="item-container">
        @if ($readLater->count() > 0)
          @foreach ($readLater as $item)
            <x-book-card :book="$item->product" :source="'readlater'" />
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
        <div x-show="activeTab === 'favorites'" x-cloak x-transition.duration.300ms>
          @include('components.book-modal', [
              'book' => $book->product,
              'showAdminActions' => false,
              'source' => 'favorites',
          ])
        </div>
      @endforeach

      @foreach ($readLater as $book)
        <div x-show="activeTab === 'readlater'" x-cloak x-transition.duration.300ms>
          @include('components.book-modal', [
              'book' => $book->product,
              'showAdminActions' => false,
              'source' => 'readlater',
          ])
        </div>
      @endforeach
    </div>
  </div>

  <script src="{{ asset('js/library-pdf.js') }}" type="module"></script>

  <script>
    function updateUrl(tab) {
      const url = new URL(window.location);
      url.searchParams.set('tab', tab);
      window.history.pushState({}, '', url);
      localStorage.setItem('activeLibraryTab', tab);
    }

    // Initialize from URL or localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const tabFromUrl = urlParams.get('tab');
      const storedTab = localStorage.getItem('activeLibraryTab');

      if (tabFromUrl) {
        window.__x.$data.activeTab = tabFromUrl;
      } else if (storedTab) {
        window.__x.$data.activeTab = storedTab;
        updateUrl(storedTab);
      }

      // Handle form submissions
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
          const hiddenField = document.createElement('input');
          hiddenField.type = 'hidden';
          hiddenField.name = 'current_tab';
          hiddenField.value = window.__x.$data.activeTab;
          this.appendChild(hiddenField);
        });
      });

      // Clear localStorage only when actually leaving the favorites page
      window.addEventListener('beforeunload', function(e) {
        if (window.location.pathname === '/favorites' && e.target.activeElement.tagName !== 'FORM') {
          localStorage.removeItem('activeLibraryTab');
        }
      });
    });
  </script>

</body>

</html>
