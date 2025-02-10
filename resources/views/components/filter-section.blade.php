@props([
    'sort' => 'newest',
    'isAdmin' => false,
    'visibility' => 'all',
])

<link rel="stylesheet" href="{{ asset('css/filter-section.css') }}">

<div x-data="filterSection">
  <div class="search-filter-container">
    <div class="search-container">
      <input type="text" id="search-input" placeholder="Search books..." x-model="searchQuery" @input="debounceSearch()">
    </div>
    <button class="mobile-filter-btn">
      <i class='bx bx-filter-alt'></i>
    </button>
    <div class="genre-dropdown">
      <button class="dropdown-btn" @click.stop="toggleGenreDropdown">
        <i class='bx bx-filter-alt'></i> Genres
      </button>
      <ul class="dropdown-content" :class="{ 'show': showGenreDropdown }" @click.stop>
        <template x-for="genre in genres" :key="genre">
          <li>
            <label class="genre-checkbox-container">
              <input type="checkbox" :value="genre" @change.stop="toggleGenre(genre)"
                :checked="selectedGenres.includes(genre)">
              <span class="custom-checkbox"></span>
              <span class="genre-name" x-text="genre"></span>
            </label>
          </li>
        </template>
      </ul>
    </div>

    <!-- Visibility Filter for Admins -->
    <div class="sort-dropdown" x-show="isAdmin">
      <button class="dropdown-btn" @click.stop="toggleVisibilityDropdown">
        <i class='bx bx-show'></i>
        <span x-text="getVisibilityDisplayText()"></span>
      </button>
      <ul class="dropdown-content" :class="{ 'show': showVisibilityDropdown }">
        <template x-for="option in visibilityOptions" :key="option.value">
          <li :data-value="option.value" :class="{ 'selected': currentVisibility === option.value }"
            @click="updateVisibility(option.value)" x-text="option.text">
          </li>
        </template>
      </ul>
    </div>

    <div class="sort-dropdown">
      <button class="dropdown-btn" @click.stop="toggleSortDropdown">
        <i class='bx bx-sort-alt-2'></i>
        <span x-text="getSortDisplayText()"></span>
      </button>
      <ul class="dropdown-content" :class="{ 'show': showSortDropdown }">
        <template x-for="option in sortOptions" :key="option.value">
          <li :data-value="option.value" :class="{ 'selected': currentSort === option.value }"
            @click="updateSort(option.value, option.text)" x-text="option.text">
          </li>
        </template>
      </ul>
      <select id="sortSelect" style="display: none;" x-model="currentSort">
        <template x-for="option in sortOptions" :key="option.value">
          <option :value="option.value" x-text="option.text"></option>
        </template>
      </select>
    </div>
  </div>

  <div class="filter-info-row" x-show="showFilterInfo">
    <span class="total-count"><span x-text="totalBooks"></span> books</span>
    <div id="active-filters">
      <template x-for="genre in selectedGenres" :key="genre">
        <span class="filter-tag" x-text="genre"></span>
      </template>
      <template x-if="searchQuery">
        <span class="filter-tag" x-text="`${totalBooks} results for '${searchQuery}'`"></span>
      </template>
      <template x-if="isAdmin && currentVisibility !== 'all'">
        <span class="filter-tag" x-text="getVisibilityDisplayText()"></span>
      </template>
    </div>
    <button class="clear-filters-btn" @click="clearAllFilters" x-show="hasActiveFilters">
      <i class='bx bx-x'></i> Clear Filters
    </button>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('filterSection', () => ({
      searchQuery: '',
      genres: [],
      selectedGenres: [],
      currentSort: '{{ $sort }}',
      isAdmin: {{ $isAdmin ? 'true' : 'false' }},
      currentVisibility: '{{ $visibility }}',
      showGenreDropdown: false,
      showSortDropdown: false,
      showVisibilityDropdown: false,
      totalBooks: 0,
      isLoading: false,
      searchTimeout: null,

      visibilityOptions: [{
          value: 'all',
          text: 'All Books'
        },
        {
          value: 'public',
          text: 'Public Only'
        },
        {
          value: 'private',
          text: 'Private Only'
        }
      ],

      sortOptions: [{
          value: 'newest',
          text: 'Newest'
        },
        {
          value: 'oldest',
          text: 'Oldest'
        },
        {
          value: 'title_asc',
          text: 'Title (A-Z)'
        },
        {
          value: 'title_desc',
          text: 'Title (Z-A)'
        },
        {
          value: 'author_asc',
          text: 'Author (A-Z)'
        },
        {
          value: 'author_desc',
          text: 'Author (Z-A)'
        },
        {
          value: 'rating_asc',
          text: 'Rating Asc'
        },
        {
          value: 'rating_desc',
          text: 'Rating Desc'
        }
      ],

      init() {
        this.fetchGenres();
        this.initFromUrl();
        this.updateTotalBooks();

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
          this.showGenreDropdown = false;
          this.showSortDropdown = false;
          this.showVisibilityDropdown = false;
        });
      },

      get hasActiveFilters() {
        return this.selectedGenres.length > 0 ||
          this.searchQuery.trim() !== '' ||
          this.currentSort !== 'newest' ||
          (this.isAdmin && this.currentVisibility !== 'all');
      },

      get showFilterInfo() {
        return this.selectedGenres.length > 0 ||
          this.searchQuery.trim() !== '' ||
          (this.isAdmin && this.currentVisibility !== 'all');
      },

      async fetchGenres() {
        try {
          const response = await fetch('/get-genres');
          this.genres = await response.json();
        } catch (error) {
          console.error('Error fetching genres:', error);
        }
      },

      initFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        this.searchQuery = urlParams.get('query') || '';
        this.selectedGenres = urlParams.get('genres') ? urlParams.get('genres').split(',') : [];
        this.currentSort = urlParams.get('sort') || 'newest';
        if (this.isAdmin) {
          this.currentVisibility = urlParams.get('visibility') || 'all';
        }
      },

      updateTotalBooks() {
        this.totalBooks = document.querySelectorAll('.pdf-item').length;
      },

      toggleGenreDropdown() {
        this.showGenreDropdown = !this.showGenreDropdown;
        this.showSortDropdown = false;
        this.showVisibilityDropdown = false;
      },

      toggleSortDropdown() {
        this.showSortDropdown = !this.showSortDropdown;
        this.showGenreDropdown = false;
        this.showVisibilityDropdown = false;
      },

      toggleVisibilityDropdown() {
        this.showVisibilityDropdown = !this.showVisibilityDropdown;
        this.showGenreDropdown = false;
        this.showSortDropdown = false;
      },

      toggleGenre(genre) {
        const index = this.selectedGenres.indexOf(genre);
        if (index === -1) {
          this.selectedGenres.push(genre);
        } else {
          this.selectedGenres.splice(index, 1);
        }
        this.updateResults();
      },

      getSortDisplayText() {
        const option = this.sortOptions.find(opt => opt.value === this.currentSort);
        return option ? option.text : 'Newest';
      },

      getVisibilityDisplayText() {
        const option = this.visibilityOptions.find(opt => opt.value === this.currentVisibility);
        return option ? option.text : 'All Books';
      },

      updateSort(value, text) {
        this.currentSort = value;
        this.showSortDropdown = false;
        this.updateResults();
      },

      updateVisibility(value) {
        this.currentVisibility = value;
        this.showVisibilityDropdown = false;
        this.updateResults();
      },

      debounceSearch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
          this.updateResults();
        }, 300);
      },

      clearAllFilters() {
        this.selectedGenres = [];
        this.searchQuery = '';
        this.currentSort = 'newest';
        if (this.isAdmin) {
          this.currentVisibility = 'all';
        }
        this.updateResults();
      },

      async updateResults(page = 1) {
        if (this.isLoading) return;
        this.isLoading = true;

        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.currentSort);

        if (this.searchQuery) {
          url.searchParams.set('query', this.searchQuery);
        } else {
          url.searchParams.delete('query');
        }

        if (this.selectedGenres.length) {
          url.searchParams.set('genres', this.selectedGenres.join(','));
        } else {
          url.searchParams.delete('genres');
        }

        if (this.isAdmin) {
          url.searchParams.set('visibility', this.currentVisibility);
        }

        url.searchParams.set('page', page);

        try {
          const response = await fetch(url.toString());
          if (!response.ok) throw new Error('Network response was not ok');

          const html = await response.text();
          const tempDiv = document.createElement('div');
          tempDiv.innerHTML = html;

          // Update the book items
          const itemContainer = document.querySelector('.item-container');
          const newItems = tempDiv.querySelector('.item-container');
          if (newItems && itemContainer) {
            itemContainer.innerHTML = newItems.innerHTML;
          }

          // Update pagination - FIXED: Ensure pagination is properly updated
          const paginationContainer = document.querySelector('.pagination-container');
          const newPagination = tempDiv.querySelector('.pagination-container');

          if (paginationContainer) {
            if (newPagination) {
              paginationContainer.innerHTML = newPagination.innerHTML;
              // Re-attach click event listeners to pagination links
              paginationContainer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', (e) => {
                  e.preventDefault();
                  const pageUrl = new URL(e.target.href);
                  const pageNum = pageUrl.searchParams.get('page');
                  this.updateResults(pageNum || 1);
                });
              });
            } else {
              // If no pagination in response, clear the container
              paginationContainer.innerHTML = '';
            }
          }

          // Update URL
          window.history.pushState({
            page
          }, '', url);

          // Update total books count
          this.updateTotalBooks();

          // Initialize thumbnails if needed
          if (typeof initializePdfThumbnails === 'function') {
            initializePdfThumbnails();
          }
        } catch (error) {
          console.error('Error:', error);
        } finally {
          this.isLoading = false;
        }
      }
    }));
  });
</script>
