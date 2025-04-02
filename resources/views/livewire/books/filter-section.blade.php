<div>
  <link rel="stylesheet" href="{{ asset('css/components/filter-section.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">

  <div class="search-filter-container">
    <div class="search-container">
      <input type="text" id="search-input" placeholder="Search books..." wire:model.live.debounce.300ms="search">
    </div>

    <div class="filter-container">
      <!-- Genre Filter Dropdown -->
      <div class="genre-dropdown" x-data="{ open: false }"
        @filter-cleared.window="$el.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)">
        <button @click.prevent="open = !open" class="filter-select genre-button">
          Genres {{ count($selectedGenres) > 0 ? '(' . count($selectedGenres) . ')' : '' }}
          <i class='bx bx-chevron-down'></i>
        </button>
        <div x-show="open" @click.outside="open = false" class="genre-dropdown-content">
          @foreach ($genres as $genre)
            <label class="genre-option">
              <input type="checkbox" wire:model.live="selectedGenres" value="{{ $genre }}">
              <span>{{ $genre }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <!-- Visibility Filter for Admins -->
      @if ($isAdmin)
        <select wire:model.live="visibility" class="filter-select" @filter-cleared.window="$el.value = 'all'">
          @foreach ($visibilityOptions as $option)
            <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
          @endforeach
        </select>
      @endif

      <!-- Sort Filter -->
      <select wire:model.live="sort" class="filter-select">
        @foreach ($sortOptions as $option)
          <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
        @endforeach
      </select>
    </div>
  </div>

  @if ($this->showFilterInfo())
    <div class="filter-info-row">
      <span class="total-count"><span>{{ $totalBooks }}</span> books</span>
      <div id="active-filters">
        @foreach ($selectedGenres as $genre)
          <span class="filter-tag">{{ $genre }}</span>
        @endforeach

        @if ($search)
          <span class="filter-tag">{{ $totalBooks }} results for '{{ $search }}'</span>
        @endif

        @if ($isAdmin && $visibility !== 'all')
          <span class="filter-tag">{{ $this->getVisibilityDisplayText() }}</span>
        @endif
      </div>

      @if ($this->hasActiveFilters())
        <button class="clear-filters-btn" wire:click="clearAllFilters">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      @endif
    </div>
  @endif

  <style>
    .filter-container {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .genre-dropdown {
      position: relative;
      display: inline-block;
    }

    .genre-button {
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;
      width: 100%;
    }

    .genre-button i {
      margin-left: 5px;
    }

    .genre-dropdown-content {
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 10;
      min-width: 200px;
      max-height: 300px;
      overflow-y: auto;
      background-color: rgb(13, 13, 13);
      border: none;
      border-radius: 4px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      margin-top: 4px;
    }

    .genre-option {
      display: flex;
      align-items: center;
      font-size: 14px;
      padding: 8px 12px;
      cursor: pointer;
      color: white;
    }

    .genre-option:hover {
      background-color: #333;
    }

    .genre-option input {
      margin-right: 8px;
    }

    @media (max-width: 768px) {
      .filter-container {
        flex-direction: column;
        width: 100%;
      }

      .filter-select,
      .genre-dropdown {
        width: 100%;
      }
    }
  </style>
</div>
