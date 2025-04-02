<div>
  <div class="text-container">
    <h1 class="text-container-title">Upload Book</h1>
  </div>
  <div class="item-container3">
    @include('books.upload-form', ['categories' => $categories])
  </div>

  <div class="text-container">
    <h1 class="text-container-title">Manage Books</h1>
    @livewire('books.filter-section', [
        'sort' => $this->sort,
        'isAdmin' => true,
        'visibility' => $this->visibility,
        'totalBooks' => $totalBooks,
    ])
  </div>

  <div style="margin-bottom: 20px;" class="item-container">
    @foreach ($books as $book)
      @livewire(
          'books.book-card',
          [
              'book' => $book,
              'showAdminActions' => true,
              'source' => 'library',
          ],
          key('book-' . $book->id)
      )
    @endforeach
  </div>

  <div class="pagination-container">
    {{ $books->onEachSide(1)->links('vendor.pagination.tailwind') }}
  </div>

  {{-- Mobile Modals --}}
  <div class="mobile-modals-container">
    @foreach ($books as $book)
      @include('components.book-modal', [
          'book' => $book,
          'showAdminActions' => true,
          'source' => 'library',
      ])
    @endforeach
  </div>

  <!-- Edit Modal -->
  <div class="edit-book-modal" style="{{ $showEditModal ? 'display: block;' : 'display: none;' }}">
    <div class="edit-book-modal-content">
      <div class="edit-book-modal-header">
        <h2>Edit Book</h2>
      </div>

      <form wire:submit.prevent="updateBook"
        @if ($editingBookId) wire:key="edit-form-{{ $editingBookId }}" @endif>
        <div class="form-group">
          <label for="title">Title:</label>
          <input type="text" id="title" wire:model.live="title" value="{{ $title }}" required>
          @error('title')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="author">Author:</label>
          <input type="text" id="author" wire:model.live="author" value="{{ $author }}" required>
          @error('author')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="category_id">Category:</label>
          <select id="category_id" wire:model.live="category_id" required>
            <option value="">Select Category</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
          @error('category_id')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="visibility-toggle">
          <label class="switch">
            <input type="checkbox" wire:model.live="is_public" {{ $is_public ? 'checked' : '' }}>
            <span class="slider round"></span>
          </label>
          <span class="visibility-label">{{ $is_public ? 'Public' : 'Private' }}</span>
        </div>

        <div class="edit-book-modal-footer">
          <button type="button" class="edit-book-btn-secondary" wire:click="resetEditForm">CANCEL</button>
          <button type="submit" class="edit-book-btn-primary">SAVE</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="delete-confirmation-modal"
    style="{{ $confirmingBookDeletion ? 'display: block;' : 'display: none;' }}">
    <div class="delete-confirmation-content">
      <div class="delete-confirmation-header">
        <h2>Delete Book</h2>
      </div>
      <div class="delete-confirmation-body">
        <p>Are you sure you want to delete <span>{{ $bookToDelete->title ?? '' }}</span> by
          <span>{{ $bookToDelete->author ?? '' }}</span>?
        </p>
        <p class="delete-confirmation-text">This action cannot be undone.</p>
      </div>
      <div class="delete-confirmation-footer">
        <button type="button" class="btn-secondary" wire:click="$set('confirmingBookDeletion', false)">Cancel</button>
        <button type="button" class="btn-delete" wire:click="deleteBook" wire:loading.attr="disabled">
          <span wire:loading.remove>Delete</span>
          <span wire:loading>Deleting...</span>
        </button>
      </div>
    </div>
  </div>

  <style>
    /* Override any potentially conflicting modal styles */
    .edit-book-modal,
    .delete-confirmation-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1050;
      overflow: auto;
    }

    .edit-book-modal-content,
    .delete-confirmation-content {
      background-color: #202020;
      margin: 0;
      border-radius: 8px;
      max-width: 500px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #555;
      border-radius: 4px;
      background-color: rgb(13, 13, 13);
      color: #fff;
    }

    .error {
      color: #ff6b6b;
      font-size: 12px;
      margin-top: 4px;
      display: block;
    }

    .edit-book-modal-header {
      margin-bottom: 15px;
    }
  </style>
</div>
