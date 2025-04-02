<div>
  <div class="text-container">
    <h1 class="text-container-title">MY COLLECTION</h1>
  </div>

  <div class="tabs-container">
    <button wire:click="switchTab('favorites')" class="tab-btn {{ $tab === 'favorites' ? 'active' : '' }}">
      <i class='bx bxs-star'></i> Favorites
    </button>
    <button wire:click="switchTab('readlater')" class="tab-btn {{ $tab === 'readlater' ? 'active' : '' }}">
      <i class='bx bxs-bookmark'></i> Read Later
    </button>
  </div>

  @if ($tab === 'favorites')
    <div class="item-container">
      @if ($favorites->count() > 0)
        @foreach ($favorites as $favorite)
          @livewire(
              'books.book-card',
              [
                  'book' => $favorite->product,
                  'source' => 'favorites',
              ],
              key('favorite-' . $favorite->id)
          )
        @endforeach
      @else
        <p class="empty-message">There are no favorites in your collection!</p>
      @endif
    </div>
  @else
    <div class="item-container">
      @if ($readLater->count() > 0)
        @foreach ($readLater as $item)
          @livewire(
              'books.book-card',
              [
                  'book' => $item->product,
                  'source' => 'readlater',
              ],
              key('readlater-' . $item->id)
          )
        @endforeach
      @else
        <p class="empty-message">There are no books in read later list!</p>
      @endif
    </div>
  @endif

  {{-- Mobile Modals --}}
  <div class="mobile-modals-container">
    @if ($tab === 'favorites')
      @foreach ($favorites as $favorite)
        @include('components.book-modal', [
            'book' => $favorite->product,
            'showAdminActions' => false,
            'source' => 'favorites',
        ])
      @endforeach
    @else
      @foreach ($readLater as $item)
        @include('components.book-modal', [
            'book' => $item->product,
            'showAdminActions' => false,
            'source' => 'readlater',
        ])
      @endforeach
    @endif
  </div>
</div>
