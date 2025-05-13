<div>
  <div>
  <div class="category-container">
    <div class="category-form">
      <h2>Add New Category</h2>
      <form wire:submit="store">
        <div class="category-add-container">
          <div class="search-container">
            <input type="text" wire:model="name" placeholder="Category Name" maxlength="30" required>
            <div class="char-count" x-data x-text="$wire.name.length + ' / 30'"
              :class="{ 'limit-reached': $wire.name.length >= 30 }"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-md">Add Category</button>
        </div>
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </form>
    </div>
</div>

<div class="category-container">

    <div class="category-list">
      <h2 style="margin-bottom: 16px;">Existing Categories</h2>

      <!-- Search and Filter Controls -->
      <div class="search-filter-container">
        <div class="search-container">
          <input type="text" wire:model.live.debounce.300ms="search" class="search-input"
            placeholder="Search categories..." autocomplete="off">
        </div>

        <!-- Mobile filter button - only visible on small screens -->
        <button class="mobile-filter-btn" id="mobileFilterBtn">
          <i class='bx bx-filter-alt'></i>
        </button>

        <div class="filter-container">
          <select wire:model.live="status" class="filter-select" wire:key="desktop-status-{{ $status }}">
            <option value="all">All Categories</option>
            <option value="assigned">Assigned Books</option>
            <option value="not-assigned">Not Assigned Books</option>
          </select>

          <select wire:model.live="visibility" class="filter-select" wire:key="desktop-visibility-{{ $visibility }}">
            <option value="all">All Visibility</option>
            <option value="public">Public Only</option>
            <option value="private">Private Only</option>
          </select>

          <select wire:model.live="sort" class="filter-select" wire:key="desktop-sort-{{ $sort }}">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="count_asc">Book Count (Low to High)</option>
            <option value="count_desc">Book Count (High to Low)</option>
          </select>
        </div>
      </div>

      <!-- Mobile Filter Drawer Component -->
      <x-filters.mobile-filter-drawer title="Filter Categories">
        <!-- Status Filter -->
        <x-filters.filter-item label="Status">
          <select wire:model.live="status" class="drawer-select" wire:key="status-{{ $status }}">
            <option value="all">All Categories</option>
            <option value="assigned">Assigned Books</option>
            <option value="not-assigned">Not Assigned Books</option>
          </select>
        </x-filters.filter-item>

        <!-- Visibility Filter -->
        <x-filters.filter-item label="Visibility">
          <select wire:model.live="visibility" class="drawer-select" wire:key="visibility-{{ $visibility }}">
            <option value="all">All Visibility</option>
            <option value="public">Public Only</option>
            <option value="private">Private Only</option>
          </select>
        </x-filters.filter-item>

        <!-- Sort Options -->
        <x-filters.filter-item label="Sort By">
          <select wire:model.live="sort" class="drawer-select" wire:key="sort-{{ $sort }}">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="count_asc">Book Count (Low to High)</option>
            <option value="count_desc">Book Count (High to Low)</option>
          </select>
        </x-filters.filter-item>

        <x-slot name="footer">
          <button class="drawer-clear-btn" wire:click="clearFilters">
            <i class='bx bx-trash'></i> Clear All Filters
          </button>
        </x-slot>
      </x-filters.mobile-filter-drawer>

      <!-- Active Filters Display -->
      @if ($hasActiveFilters)
        <div class="filter-info-row">
          <span class="total-count">{{ $totalCategories }} categories</span>
          <div class="active-filters">
            @if ($search)
              <span class="filter-tag">Search: {{ $search }}</span>
            @endif
            @if ($status !== 'all')
              <span class="filter-tag">{{ $this->getStatusText() }}</span>
            @endif
            @if ($visibility !== 'all')
              <span class="filter-tag">{{ $this->getVisibilityText() }}</span>
            @endif
            @if ($sort !== 'newest')
              <span class="filter-tag">{{ $this->getSortText() }}</span>
            @endif
          </div>
          <button wire:click="clearFilters" class="clear-filters-btn">
            <i class='bx bx-x'></i> Clear Filters
          </button>
        </div>
      @endif

      <!-- Loading State -->
      <div wire:loading.delay wire:target="search, status, visibility, sort, page" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Loading categories...</p>
      </div>

      <!-- No Results Message -->
      @if ($categories->isEmpty())
        <div class="no-results">
          No categories found
        </div>
      @endif

      <!-- Categories List -->
      @foreach ($categories as $category)
        <div class="category-item">
          <div class="category-content">
            @if ($editingCategoryId !== $category->id)
              <div class="category-display">
                <h3>
                  <span>{{ $category->name }}</span>
                  <span class="book-count">({{ $category->products_count }} books)</span>
                  @if ($category->is_system)
                    <span class="system-badge">System</span>
                  @endif
                </h3>
              </div>
            @else
              <div class="category-edit-form">
                <form wire:submit="updateCategory">
                  <input type="text" wire:model="editingCategoryName" class="edit-input" maxlength="30" required>
                  <div class="edit-buttons">
                    <button type="submit" class="btn-category-primary">Save</button>
                    <button type="button" wire:click="cancelEditing" class="btn-category-secondary">Cancel</button>
                  </div>
                  @error('editingCategoryName')
                    <div class="error-message">{{ $message }}</div>
                  @enderror
                </form>
              </div>
            @endif
          </div>

          <div class="btn-container-cat">
            @if ($editingCategoryId !== $category->id && !$category->is_system)
              <button wire:click="toggleVisibility({{ $category->id }})"
                class="btn-visibility {{ $category->is_public ? 'public' : 'private' }}"
                title="{{ $category->is_public ? 'Make Private' : 'Make Public' }}">
                <i class='bx {{ $category->is_public ? 'bx-show' : 'bx-hide' }}'></i>
              </button>
              <button wire:click="startEditing({{ $category->id }})" class="btn-edit-cat">
                <i class='bx bx-edit-alt'></i>
              </button>
              <button wire:click="confirmDelete({{ $category->id }})" class="btn-delete-cat">
                <i class='bx bx-trash'></i>
              </button>
            @endif
          </div>
        </div>
      @endforeach

      <!-- Pagination -->
      <div class="mt-4">
        {{ $categories->links('vendor.pagination.tailwind') }}
      </div>
    </div>
</div>

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal && $categoryToDelete)
      <div class="delete-confirmation-modal">
        <div class="delete-confirmation-content">
          <div class="delete-confirmation-header">
            <h2>Delete Category</h2>
          </div>
          <div class="delete-confirmation-body">
            <p>Are you sure you want to delete {{ $categoryToDelete->name }}?</p>
            <div class="reassign-section">
              <p>This category has {{ $categoryToDelete->products_count }}
                {{ $categoryToDelete->products_count === 1 ? 'book' : 'books' }}.
                @if ($categoryToDelete->products_count > 0)
                  Please select a new category for these books:
                @endif
              </p>
              @if ($categoryToDelete->products_count > 0)
                <select wire:model.live="selectedNewCategoryId" class="reassign-select">
                  <option value="">Select a category</option>
                  @foreach ($availableCategories as $category)
                    <option value="{{ $category->id }}">
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
                @error('selectedNewCategoryId')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              @endif
            </div>
            <p class="delete-confirmation-text">This action cannot be undone.</p>
          </div>
          <div class="delete-confirmation-footer">
            <button type="button" class="btn btn-ghost btn-md" wire:click="cancelDelete">Cancel</button>

            @if ($categoryToDelete->products_count > 0)
              @if ($selectedNewCategoryId)
                <button type="button" class="btn btn-danger btn-md" wire:click="deleteCategory" wire:loading.attr="disabled"
                  wire:target="deleteCategory">
                  Delete
                </button>
              @else
                <button type="button" class="btn-delete-disabled">
                  Select Category First
                </button>
              @endif
            @else
              <button type="button" class="btn btn-danger btn-md" wire:click="deleteCategory" wire:loading.attr="disabled"
                wire:target="deleteCategory">
                Delete
              </button>
            @endif
          </div>
        </div>
      </div>
    @endif
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const updateCharCounter = () => {
      const input = document.querySelector('input[wire\\:model="name"]');
      if (input) {
        const charCount = document.querySelector('.char-count');
        if (charCount) {
          const count = input.value.length;
          const maxLength = input.getAttribute('maxlength') || 30;
          charCount.textContent = `${count} / ${maxLength}`;
          charCount.classList.toggle('limit-reached', count >= maxLength);
        }
      }
    };

    updateCharCounter();

    // Listen for input events on the text field
    document.addEventListener('input', (e) => {
      if (e.target.matches('input[wire\\:model="name"]')) {
        updateCharCounter();
      }
    });

    // Listen for Livewire updates
    document.addEventListener('livewire:initialized', () => {
      Livewire.hook('message.processed', () => {
        updateCharCounter();
      });
    });
  });
</script>
