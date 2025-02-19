@include('components.alert')
@include('navbar')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>
<script src="{{ asset('js/components/delete-modal.js') }}"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">

<script>
  // Set up Axios CSRF token and base URL
  axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
    'content');
  axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  axios.defaults.withCredentials = true;
</script>

<div class="main-container">
  <div class="category-container">
    <h1
      style="margin-bottom: 20px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 32px;">
      Manage Categories</h1>


    <!-- New Category Form Section -->
    <div class="category-form">
      <h2>Add New Category</h2>
      <form action="{{ route('categories.store') }}" method="POST" x-data="{ charCount: 0 }">
        @csrf
        <div class="category-add-container">
          <div class="search-container">
            <input type="text" name="name" placeholder="Category Name" maxlength="30" required
              @input="charCount = $event.target.value.length" class="@error('name') is-invalid @enderror"
              value="{{ old('name') }}">
            <div class="char-count" :class="{ 'limit-reached': charCount >= 30 }" x-text="`${charCount} / 30`"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-md">Add Category</button>
        </div>
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </form>
    </div>

    <div class="category-list" x-data="categoryManager()" @categories-updated.window="fetchCategories">
      <h2 style="margin-bottom: 16px;">Existing Categories</h2>

      <!-- Search and Filter Controls -->
      <div class="search-filter-container">
        <div class="search-container">
          <input type="text" class="search-input" x-model="filters.search" placeholder="Search categories..."
            autocomplete="off">
        </div>

        <div class="filter-dropdown">
          <button class="btn btn-filter btn-md" @click="toggleDropdown('filter')">
            <i class='bx bx-filter-alt'></i> Filter
          </button>
          <ul class="dropdown-content" :class="{ 'show': dropdowns.filter }">
            <li @click="setFilter('all')" :class="{ 'selected': filters.status === 'all' }">All Categories</li>
            <li @click="setFilter('assigned')" :class="{ 'selected': filters.status === 'assigned' }">Assigned Books
            </li>
            <li @click="setFilter('not-assigned')" :class="{ 'selected': filters.status === 'not-assigned' }">Not
              Assigned Books</li>
          </ul>
        </div>

        <div class="filter-dropdown">
          <button class="btn btn-filter btn-md" @click="toggleDropdown('sort')">
            <i class='bx bx-sort-alt-2'></i> Sort
          </button>
          <ul class="dropdown-content" :class="{ 'show': dropdowns.sort }">
            <li @click="setSort('newest')" :class="{ 'selected': filters.sort === 'newest' }">Newest</li>
            <li @click="setSort('oldest')" :class="{ 'selected': filters.sort === 'oldest' }">Oldest</li>
            <li @click="setSort('count_asc')" :class="{ 'selected': filters.sort === 'count_asc' }">Book Count (Low to
              High)</li>
            <li @click="setSort('count_desc')" :class="{ 'selected': filters.sort === 'count_desc' }">Book Count (High
              to Low)</li>
          </ul>
        </div>
      </div>

      <!-- Active Filters Display -->
      <div class="filter-info-row" x-show="hasActiveFilters">
        <span class="total-count"><span x-text="total"></span> categories</span>
        <div class="active-filters">
          <template x-if="filters.search">
            <span class="filter-tag">Search: <span x-text="filters.search"></span></span>
          </template>
          <template x-if="filters.status !== 'all'">
            <span class="filter-tag" x-text="getStatusText(filters.status)"></span>
          </template>
          <template x-if="filters.sort !== 'newest'">
            <span class="filter-tag" x-text="getSortText(filters.sort)"></span>
          </template>
        </div>
        <button @click="clearFilters" class="clear-filters-btn">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      </div>

      <!-- Loading indicator -->
      <div x-show="isLoading" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Loading categories...</p>
      </div>

      <!-- No results message -->
      <div x-show="!isLoading && !categories.length" class="no-results">
        No categories found
      </div>

      <!-- Categories list with edit/delete buttons -->
      <div x-show="!isLoading && categories.length">
        <template x-for="category in categories" :key="category.id">
          <div class="category-item">
            <div class="category-content">
              <template x-if="!category.editing">
                <div class="category-display">
                  <h3>
                    <span x-text="category.name"></span>
                    <span class="book-count" x-text="`(${category.products_count || 0} books)`"></span>
                  </h3>
                </div>
              </template>

              <template x-if="category.editing">
                <div class="category-edit-form">
                  <form @submit.prevent="handleEditSubmit(category)" class="edit-form">
                    <input type="text" x-model="category.editName" class="edit-input"
                      :class="{ 'is-invalid': category.error }" maxlength="30" required>
                    <div class="edit-buttons">
                      <button type="submit" class="btn-category-primary">Save</button>
                      <button type="button" @click="cancelEdit(category)"
                        class="btn-category-secondary">Cancel</button>
                    </div>
                    <div x-show="category.error" x-text="category.error" class="error-message"></div>
                  </form>
                </div>
              </template>
            </div>

            <div class="btn-container-cat" x-show="!category.editing">
              <button @click="startEdit(category)" class="btn-edit-cat">
                <i class='bx bx-edit-alt'></i>
              </button>
              <button type="button" class="btn-delete-cat"
                @click="$dispatch('open-delete-modal', {
                  item: category,
                  callback: async () => {
                    try {
                      await axios.delete(`/categories/${category.id}`);
                      await fetchCategories();
                      window.dispatchEvent(new CustomEvent('alert', {
                        detail: { type: 'success', message: 'Category deleted successfully' }
                      }));
                    } catch (error) {
                      window.dispatchEvent(new CustomEvent('alert', {
                        detail: { type: 'error', message: error.response?.data?.message || 'Failed to delete category' }
                      }));
                    }
                  }
                })">
                <i class='bx bx-trash'></i>
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<x-delete-confirmation-modal title="Delete Category" />

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('categoryManager', () => ({
      categories: [],
      filters: {
        search: '',
        status: 'all',
        sort: 'newest'
      },
      dropdowns: {
        filter: false,
        sort: false
      },
      total: 0,
      isLoading: true,

      // Data Fetching

      async fetchCategories() {
        this.isLoading = true;
        try {
          const response = await axios.get('/categories/search', {
            params: this.filters
          });
          this.categories = response.data.categories;
          this.total = response.data.total;
        } catch (error) {
          console.error('Error fetching categories:', error);
        } finally {
          this.isLoading = false;
        }
      },

      // Initialization and Event Handlers

      init() {
        // Load categories immediately when component initializes
        this.fetchCategories();

        this.$watch('filters', debounce(() => this.fetchCategories(), 300));

        document.addEventListener('click', (e) => {
          if (!e.target.closest('.filter-dropdown')) {
            this.dropdowns.filter = false;
            this.dropdowns.sort = false;
          }
        });
      },

      // Dropdown Management

      toggleDropdown(type) {
        this.dropdowns[type] = !this.dropdowns[type];
        const other = type === 'filter' ? 'sort' : 'filter';
        this.dropdowns[other] = false;
      },

      // Category Edit Operations

      async handleEditSubmit(category) {
        try {
          const response = await axios.put(`/categories/${category.id}`, {
            name: category.editName
          });

          if (response.data.category) {
            // Update the category in the list with the response data
            const index = this.categories.findIndex(c => c.id === category.id);
            if (index !== -1) {
              this.categories[index] = {
                ...this.categories[index],
                ...response.data.category,
                editing: false,
                error: null
              };
            }
            // Display success message
            const alertEvent = new CustomEvent('alert', {
              detail: {
                type: 'success',
                message: 'Category updated successfully'
              }
            });
            window.dispatchEvent(alertEvent);
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            category.error = error.response.data.errors.name[0];
          } else {
            console.error('Error updating category:', error);
            category.error = 'An error occurred while updating the category.';
          }
        }
      },


      startEdit(category) {
        // Reset any previous errors
        category.error = null;
        category.editing = true;
        category.editName = category.name;
      },

      cancelEdit(category) {
        category.editing = false;
        category.editName = category.name;
        category.error = null;
      },

      // Filter Management

      get hasActiveFilters() {
        return this.filters.search ||
          this.filters.status !== 'all' ||
          this.filters.sort !== 'newest';
      },

      setFilter(status) {
        this.filters.status = status;
        this.dropdowns.filter = false;
      },

      setSort(sort) {
        this.filters.sort = sort;
        this.dropdowns.sort = false;
      },

      clearFilters() {
        this.filters = {
          search: '',
          status: 'all',
          sort: 'newest'
        };
      },

      // Helper Functions for Text Display

      getStatusText(status) {
        return {
          'assigned': 'Assigned Books',
          'not-assigned': 'Not Assigned Books'
        } [status] || 'All Categories';
      },

      getSortText(sort) {
        return {
          'oldest': 'Oldest First',
          'count_asc': 'Book Count (Low to High)',
          'count_desc': 'Book Count (High to Low)'
        } [sort] || 'Newest First';
      },

      async handleNewCategory(event) {
        try {
          const formData = new FormData(event.target);
          const response = await axios.post('/categories', formData);

          if (response.data.category) {
            // Clear the input
            this.$refs.categoryInput.value = '';
            this.charCount = 0;

            // Fetch updated categories list to maintain sort order
            await this.fetchCategories();

            // Display success message
            const alertEvent = new CustomEvent('alert', {
              detail: {
                type: 'success',
                message: 'Category created successfully'
              }
            });
            window.dispatchEvent(alertEvent);
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            const alertEvent = new CustomEvent('alert', {
              detail: {
                type: 'error',
                message: error.response.data.errors.name[0]
              }
            });
            window.dispatchEvent(alertEvent);
          }
        }
      }
    }));
  });

  // Debounce function to prevent spamming API
  function debounce(fn, wait) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), wait);
    };
  }
</script>
