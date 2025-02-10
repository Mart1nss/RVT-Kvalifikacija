<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Upload Page</title>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/product-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-edit.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

@include('components.alert')
@include('navbar')

<div class="main-container">


  <div class="text-container">
    <h1 class="text-container-title">Upload Book</h1>
  </div>
  <div class="item-container3">
    @include('components.book-upload-form')
  </div>

  <div class="text-container">
    <h1 class="text-container-title">Manage Books</h1>

    <div class="search-filter-container">
      <div class="search-container">
        <input type="text" id="search-input" placeholder="Search books...">
      </div>
      <button class="mobile-filter-btn">
        <i class='bx bx-filter-alt'></i>
      </button>
      <div class="genre-filter-container">
        <div class="genre-dropdown">
          <button class="dropdown-btn">Filter by Genres</button>
          <ul class="dropdown-content">
            <!-- Genres -->
            <div class="dropdown-footer">
              <button type="button" class="clear-filters">Clear</button>
              <button type="button" class="apply-filters">Apply</button>
            </div>
          </ul>
        </div>
      </div>
      <div class="visibility-filter" style="margin-left: 15px;">
        <select id="visibilityFilter" onchange="applyVisibilityFilter()"
          style="padding: 8px; border-radius: 8px; background: #1c1a1a; color: white; border: none; height: 41px; text-transform: uppercase; font-weight: 800; font-size: 12px;">
          <option value="all" {{ $visibility == 'all' ? 'selected' : '' }}>All Books</option>
          <option value="public" {{ $visibility == 'public' ? 'selected' : '' }}>Public Only</option>
          <option value="private" {{ $visibility == 'private' ? 'selected' : '' }}>Private Only</option>
        </select>
      </div>
      <div class="sort-dropdown">
        <select id="sortSelect" onchange="applySorting()">
          <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest First</option>
          <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest First</option>
          <option value="title_asc" {{ $sort == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
          <option value="title_desc" {{ $sort == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
          <option value="author_asc" {{ $sort == 'author_asc' ? 'selected' : '' }}>Author (A-Z)</option>
          <option value="author_desc" {{ $sort == 'author_desc' ? 'selected' : '' }}>Author (Z-A)</option>
          <option value="rating_asc" {{ $sort == 'rating_asc' ? 'selected' : '' }}>Rating (Low-High)</option>
          <option value="rating_desc" {{ $sort == 'rating_desc' ? 'selected' : '' }}>Rating (High-Low)</option>
        </select>
      </div>
    </div>
  </div>

  <div style="margin-bottom: 20px;" class="item-container">
    @foreach ($data as $book)
      <x-book-card :book="$book" :showAdminActions="true" :source="'library'" />
    @endforeach
  </div>

  <div class="pagination-container">
    {{ $data->onEachSide(1)->links() }}
  </div>

  {{-- Mobile Modals --}}
  <div class="mobile-modals-container">
    @foreach ($data as $book)
      @include('components.book-modal', ['book' => $book, 'showAdminActions' => true])
    @endforeach
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="edit-book-modal">
    <div class="edit-book-modal-content">
      <div class="edit-book-modal-header">
        <h2>Edit Book</h2>
      </div>
      <form id="editForm" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
          <option value="">Select Category</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
          @endforeach
        </select>
        <div class="visibility-toggle">
          <label class="switch">
            <input type="checkbox" name="is_public" id="edit_is_public">
            <span class="slider round"></span>
          </label>
          <span class="visibility-label">Public</span>
        </div>
        <div class="edit-book-modal-footer">
          <button type="button" class="edit-book-btn-secondary" onclick="closeEditModal()">CANCEL</button>
          <button type="submit" class="edit-book-btn-primary">SAVE</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="delete-confirmation-modal">
    <div class="delete-confirmation-content">
      <div class="delete-confirmation-header">
        <h2>Delete Book</h2>
      </div>
      <div class="delete-confirmation-body">
        <p>Are you sure you want to delete <span id="deleteBookDetails"></span></p>
        <p class="delete-confirmation-text">This action cannot be undone.</p>
      </div>
      <div class="delete-confirmation-footer">
        <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
        <button type="button" class="btn-delete" id="confirmDeleteBtn" onclick="submitDelete()">Delete</button>
      </div>
    </div>
  </div>

  <div id="alertContainer"></div>

</div>

<script src="{{ asset('js/book-manage-modals.js') }}"></script>
<script src="{{ asset('js/book-manage-filters.js') }}"></script>
<script src="{{ asset('js/library-pdf.js') }}" type="module"></script>

</body>

</html>
