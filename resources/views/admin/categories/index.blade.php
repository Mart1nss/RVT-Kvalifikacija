@include('components.alert')
@include('navbar')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
<style>
  .book-count {
    font-size: 0.8em;
    color: #888;
    font-weight: normal;
    margin-left: 8px;
    display: inline-block;
  }
</style>

<div class="main-container">
  <div class="category-container">
    <h1
      style="margin-bottom: 20px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 32px;">
      Manage Categories</h1>

    <div class="category-form">
      <h2>Add New Category</h2>
      <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="category-add-container">
          <div class="search-container">
            <input type="text" name="name" placeholder="Category Name" required>
          </div>
          <button type="submit" class="btn btn-primary btn-md">Add Category</button>
        </div>
      </form>
    </div>

    <div class="category-list">
      <h2 style="margin-bottom: 16px;">Existing Categories</h2>
      <div class="search-filter-container">
        <div class="search-container">
          <input type="text" class="search-input" id="categorySearch" placeholder="Search categories..."
            autocomplete="off">
        </div>

        <div class="filter-dropdown">
          <button class="btn btn-filter btn-md" id="filterBtn">
            <i class='bx bx-filter-alt'></i> Filter
          </button>
          <ul class="dropdown-content" id="filterDropdown">
            <li data-value="all" class="selected">All Categories</li>
            <li data-value="assigned">Assigned Books</li>
            <li data-value="not-assigned">Not Assigned Books</li>
          </ul>
        </div>

        <div class="filter-dropdown">
          <button class="btn btn-filter btn-md" id="sortBtn">
            <i class='bx bx-sort-alt-2'></i> Sort
          </button>
          <ul class="dropdown-content" id="sortDropdown">
            <li data-value="newest" class="selected">Newest</li>
            <li data-value="oldest">Oldest</li>
            <li data-value="count_asc">Book Count (Low to High)</li>
            <li data-value="count_desc">Book Count (High to Low)</li>
          </ul>
        </div>
      </div>

      <div class="filter-info-row hidden" id="filterInfoRow">
        <span class="total-count"><span id="totalCategories">0</span> categories</span>
        <div id="active-filters"></div>
        <button class="clear-filters-btn" id="clearFiltersBtn">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      </div>

      <div id="no-results" class="no-results hidden">
        No categories found
      </div>

      @foreach ($categories as $category)
        <div class="category-item" data-category-name="{{ strtolower($category->name) }}"
          data-book-count="{{ $category->products_count ?? 0 }}" data-created-at="{{ $category->created_at }}">
          <div class="category-content">
            <div class="category-display">
              <h3>
                {{ $category->name }}
                <span class="book-count">
                  ({{ $category->products_count ?? 0 }} books)
                </span>
              </h3>
            </div>
            <div class="category-edit-form" style="display: none;">
              <input type="text" class="edit-input" value="{{ $category->name }}" required>
              <div class="edit-buttons">
                <button type="button" class="btn-category-primary save-edit"
                  data-category-id="{{ $category->id }}">Save</button>
                <button type="button" class="btn-category-secondary cancel-edit">Cancel</button>
              </div>
            </div>
          </div>
          <div class="btn-container-cat">
            <button class="btn-edit-cat" onclick="toggleEdit(this, {{ $category->id }})"><i
                class='bx bx-edit-alt'></i></button>
            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;"
              class="delete-form">
              @csrf
              @method('DELETE')
              <button type="button" class="btn-delete-cat"
                onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')">
                <i class='bx bx-trash'></i>
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-confirmation-modal">
  <div class="delete-confirmation-content">
    <div class="delete-confirmation-header">
      <h2>Delete Category</h2>
    </div>
    <div class="delete-confirmation-body">
      <p>Are you sure you want to delete "<span id="categoryName"></span>" category ?</p>
      <p class="delete-confirmation-text">This action cannot be undone.</p>
    </div>
    <div class="delete-confirmation-footer">
      <button type="button" class="btn-category-secondary" onclick="closeModal()">Cancel</button>
      <button type="button" class="btn-delete" id="confirmDeleteBtn">Delete</button>
    </div>
  </div>
</div>

<script>
  // Category management functionality
  function toggleEdit(button, categoryId) {
    const categoryItem = button.closest('.category-item');
    const displayDiv = categoryItem.querySelector('.category-display');
    const editForm = categoryItem.querySelector('.category-edit-form');
    const editInput = categoryItem.querySelector('.edit-input');
    const btnContainer = categoryItem.querySelector('.btn-container-cat');

    if (displayDiv.style.display !== 'none') {
      displayDiv.style.display = 'none';
      btnContainer.style.display = 'none';
      editForm.style.display = 'flex';
      editInput.focus();
    }
  }

  // Filter and Sort functionality
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('categorySearch');
    const filterBtn = document.getElementById('filterBtn');
    const sortBtn = document.getElementById('sortBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const sortDropdown = document.getElementById('sortDropdown');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const filterInfoRow = document.getElementById('filterInfoRow');
    const activeFilters = document.getElementById('active-filters');
    const totalCategories = document.getElementById('totalCategories');
    const noResults = document.getElementById('no-results');
    const categoryItems = document.querySelectorAll('.category-item');

    let currentFilter = 'all';
    let currentSort = 'newest';
    let searchQuery = '';

    // Initialize total count
    updateTotalCount();

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.filter-dropdown')) {
        filterDropdown.classList.remove('show');
        sortDropdown.classList.remove('show');
      }
    });

    // Toggle dropdowns
    filterBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      filterDropdown.classList.toggle('show');
      sortDropdown.classList.remove('show');
    });

    sortBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      sortDropdown.classList.toggle('show');
      filterDropdown.classList.remove('show');
    });

    // Filter selection
    filterDropdown.addEventListener('click', function(e) {
      const item = e.target.closest('li');
      if (!item) return;

      currentFilter = item.dataset.value;
      updateSelectedItem(filterDropdown, currentFilter);
      filterCategories();
      updateFilterInfo();
    });

    // Sort selection
    sortDropdown.addEventListener('click', function(e) {
      const item = e.target.closest('li');
      if (!item) return;

      currentSort = item.dataset.value;
      updateSelectedItem(sortDropdown, currentSort);
      sortCategories();
      updateFilterInfo();
    });

    // Search functionality
    let searchTimeout;
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        searchQuery = this.value.toLowerCase().trim();
        filterCategories();
        updateFilterInfo();
      }, 300);
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
      currentFilter = 'all';
      currentSort = 'newest';
      searchQuery = '';
      searchInput.value = '';
      updateSelectedItem(filterDropdown, 'all');
      updateSelectedItem(sortDropdown, 'newest');
      filterCategories();
      updateFilterInfo();
    });

    function updateSelectedItem(dropdown, value) {
      dropdown.querySelectorAll('li').forEach(li => {
        li.classList.toggle('selected', li.dataset.value === value);
      });
    }

    function filterCategories() {
      let visibleCount = 0;
      const items = document.querySelectorAll('.category-item');

      items.forEach(item => {
        const name = item.dataset.categoryName;
        const bookCount = parseInt(item.dataset.bookCount);
        let isVisible = true;

        // Apply search filter
        if (searchQuery && !name.includes(searchQuery)) {
          isVisible = false;
        }

        // Apply category filter
        if (isVisible && currentFilter !== 'all') {
          if (currentFilter === 'assigned' && bookCount === 0) {
            isVisible = false;
          } else if (currentFilter === 'not-assigned' && bookCount > 0) {
            isVisible = false;
          }
        }

        item.classList.toggle('hidden', !isVisible);
        if (isVisible) visibleCount++;
      });

      noResults.classList.toggle('hidden', visibleCount > 0);
      sortCategories();
      updateTotalCount();
    }

    function sortCategories() {
      const categoryList = document.querySelector('.category-list');
      const items = Array.from(document.querySelectorAll('.category-item'));
      const sortedItems = items.sort((a, b) => {
        switch (currentSort) {
          case 'newest':
            return new Date(b.dataset.createdAt) - new Date(a.dataset.createdAt);
          case 'oldest':
            return new Date(a.dataset.createdAt) - new Date(b.dataset.createdAt);
          case 'count_asc':
            return parseInt(a.dataset.bookCount) - parseInt(b.dataset.bookCount);
          case 'count_desc':
            return parseInt(b.dataset.bookCount) - parseInt(a.dataset.bookCount);
          default:
            return 0;
        }
      });

      // Reorder elements
      sortedItems.forEach(item => {
        categoryList.appendChild(item);
      });
    }

    function updateFilterInfo() {
      const hasActiveFilters = searchQuery || currentFilter !== 'all' || currentSort !== 'newest';
      filterInfoRow.classList.toggle('hidden', !hasActiveFilters);

      // Update active filters
      activeFilters.innerHTML = '';

      if (searchQuery) {
        activeFilters.innerHTML += `<span class="filter-tag">Search: ${searchQuery}</span>`;
      }

      if (currentFilter !== 'all') {
        const filterText = currentFilter === 'assigned' ? 'Assigned Books' : 'Not Assigned Books';
        activeFilters.innerHTML += `<span class="filter-tag">${filterText}</span>`;
      }

      if (currentSort !== 'newest') {
        const sortText = sortDropdown.querySelector(`[data-value="${currentSort}"]`).textContent;
        activeFilters.innerHTML += `<span class="filter-tag">Sort: ${sortText}</span>`;
      }
    }

    function updateTotalCount() {
      const visibleItems = document.querySelectorAll('.category-item:not(.hidden)').length;
      totalCategories.textContent = visibleItems;
    }

    // Initial sort
    sortCategories();
  });

  // Modal functionality
  let currentForm = null;

  function confirmDelete(categoryId, categoryName) {
    const modal = document.getElementById('deleteModal');
    const categoryNameSpan = document.getElementById('categoryName');
    const confirmBtn = document.getElementById('confirmDeleteBtn');

    currentForm = event.target.closest('form');
    categoryNameSpan.textContent = categoryName;
    modal.style.display = 'block';

    confirmBtn.onclick = function() {
      if (currentForm) {
        currentForm.submit();
      }
    }
  }

  function closeModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
    currentForm = null;
  }

  // Close modal when clicking outside
  window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
      closeModal();
    }
  }

  // Add event listeners for save and cancel buttons
  document.querySelectorAll('.cancel-edit').forEach(button => {
    button.addEventListener('click', function() {
      const categoryItem = this.closest('.category-item');
      const displayDiv = categoryItem.querySelector('.category-display');
      const editForm = categoryItem.querySelector('.category-edit-form');
      const btnContainer = categoryItem.querySelector('.btn-container-cat');

      displayDiv.style.display = 'block';
      editForm.style.display = 'none';
      btnContainer.style.display = 'flex';
    });
  });

  document.querySelectorAll('.save-edit').forEach(button => {
    button.addEventListener('click', function() {
      const categoryId = this.dataset.categoryId;
      const categoryItem = this.closest('.category-item');
      const editInput = categoryItem.querySelector('.edit-input');
      const newName = editInput.value;

      // Create and submit form
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/categories/${categoryId}`;

      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'PUT';

      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken;

      const nameInput = document.createElement('input');
      nameInput.type = 'hidden';
      nameInput.name = 'name';
      nameInput.value = newName;

      form.appendChild(methodInput);
      form.appendChild(csrfInput);
      form.appendChild(nameInput);

      document.body.appendChild(form);
      form.submit();
    });
  });
</script>
