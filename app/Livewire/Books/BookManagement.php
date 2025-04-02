<?php

namespace App\Livewire\Books;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;

class BookManagement extends Component
{
  use WithPagination;

  // Filter properties
  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';
  public $visibility = 'all';

  // Edit form properties
  public $editingBook = null;
  public $title = '';
  public $author = '';
  public $category_id = '';
  public $is_public = false;
  public $showEditModal = false;

  // Delete confirmation
  public $confirmingBookDeletion = false;
  public $bookToDelete = null;

  // Optimization - prefetch books for faster modal loading
  protected $books = [];
  protected $categories = [];

  // Listeners for events from child components
  protected $listeners = [
    'refreshBooks' => '$refresh',
    'filterUpdated' => 'updateFilters',
    'editBook' => 'editBook',
    'confirmBookDeletion' => 'confirmBookDeletion'
  ];

  /**
   * Force refresh the books list
   */
  public function refreshBooks()
  {
    // This will trigger a re-render of the component
    $this->dispatch('$refresh');
  }

  public function mount()
  {
    $this->categories = Category::all();
    $this->resetPage();

    // Initialize from query parameters if they exist
    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];
    $this->sort = request()->query('sort', 'newest');
    $this->visibility = request()->query('visibility', 'all');
  }

  public function updateFilters($filters)
  {
    $this->search = $filters['search'] ?? '';
    $this->selectedGenres = $filters['selectedGenres'] ?? [];
    $this->sort = $filters['sort'] ?? 'newest';
    $this->visibility = $filters['visibility'] ?? 'all';
    $this->resetPage();
  }

  /**
   * Handle edit book request
   * 
   * @param mixed $bookId Book ID or array containing bookId
   */
  public function editBook($bookId = null)
  {
    // Extract book ID from array or use directly
    if (is_array($bookId) && isset($bookId['bookId'])) {
      $bookId = $bookId['bookId'];
    }

    // Return early if no book ID
    if (!$bookId) {
      $this->dispatch('alert', [
        'type' => 'error',
        'message' => 'No book ID provided'
      ]);
      return;
    }

    try {
      $book = Product::findOrFail($bookId);

      // Reset form fields first to ensure clean state
      $this->resetEditForm();

      // Set the form values from the book data
      $this->editingBook = $book;
      $this->title = $book->title;
      $this->author = $book->author;
      $this->category_id = (string) $book->category_id;
      $this->is_public = (bool) $book->is_public;

      // Open the modal
      $this->showEditModal = true;
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type' => 'error',
        'message' => "Error finding book: " . $e->getMessage()
      ]);
    }
  }

  /**
   * Confirm book deletion
   * 
   * @param mixed $bookId Book ID or array containing bookId
   */
  public function confirmBookDeletion($bookId = null)
  {
    // Extract book ID from array or use directly
    if (is_array($bookId) && isset($bookId['bookId'])) {
      $bookId = $bookId['bookId'];
    }

    // Return early if no book ID
    if (!$bookId)
      return;

    $book = Product::find($bookId);
    if (!$book)
      return;

    $this->confirmingBookDeletion = true;
    $this->bookToDelete = $book;
  }

  public function resetEditForm()
  {
    $this->editingBook = null;
    $this->title = '';
    $this->author = '';
    $this->category_id = '';
    $this->is_public = false;
    $this->showEditModal = false;
    $this->resetErrorBag();
  }

  public function updateBook()
  {
    if (!$this->editingBook) {
      $this->dispatch('alert', [
        'type' => 'error',
        'message' => 'No book selected for editing'
      ]);
      return;
    }

    $this->validate([
      'title' => 'required|string|max:255',
      'author' => 'required|string|max:255',
      'category_id' => 'required|exists:categories,id',
    ]);

    // Store old values before update
    $changes = [];

    if ($this->editingBook->title !== $this->title) {
      $changes[] = "title from '{$this->editingBook->title}' to '{$this->title}'";
    }

    if ($this->editingBook->author !== $this->author) {
      $changes[] = "author from '{$this->editingBook->author}' to '{$this->author}'";
    }

    if ((int) $this->editingBook->category_id !== (int) $this->category_id) {
      $oldCategory = Category::find($this->editingBook->category_id);
      $newCategory = Category::find($this->category_id);
      if ($oldCategory && $newCategory) {
        $changes[] = "category from '{$oldCategory->name}' to '{$newCategory->name}'";
      }
    }

    $oldIsPublic = (bool) $this->editingBook->is_public;
    $newIsPublic = (bool) $this->is_public;

    if ($oldIsPublic !== $newIsPublic) {
      $oldVisibility = $oldIsPublic ? 'public' : 'private';
      $newVisibility = $newIsPublic ? 'public' : 'private';
      $changes[] = "visibility from {$oldVisibility} to {$newVisibility}";
    }

    try {
      $this->editingBook->title = $this->title;
      $this->editingBook->author = $this->author;
      $this->editingBook->category_id = $this->category_id;
      $this->editingBook->is_public = $newIsPublic;

      $this->editingBook->save();

      if (!empty($changes)) {
        $changeDescription = "Changed " . implode(', ', $changes);
        app(AuditLogService::class)->log(
          "Updated book",
          "book",
          $changeDescription,
          $this->editingBook->id,
          $this->editingBook->title
        );
      }

      // Store the updated book ID for state management
      $updatedBookId = $this->editingBook->id;

      $this->resetEditForm();

      // Refresh the books list to reflect changes
      $this->dispatch('refreshBooks');

      // Display success alert
      $this->dispatch(
        'alert',
        // Sending as a single object with message and type properties
        [
          'type' => 'success',
          'message' => 'Book updated successfully'
        ]
      );
    } catch (\Exception $e) {
      $this->dispatch(
        'alert',
        // Same format as success alert
        [
          'type' => 'error',
          'message' => 'Error updating book: ' . $e->getMessage()
        ]
      );
    }
  }

  public function hydrate()
  {
    // This method runs after Livewire hydrates the component from a dehydrated state
    // If we have an editing book ID but no title/author, re-fetch the data
    if ($this->editingBook && empty($this->title)) {
      $book = null;
      if (is_object($this->editingBook)) {
        $book = Product::find($this->editingBook->id);
      } elseif (is_numeric($this->editingBook)) {
        $book = Product::find($this->editingBook);
      }

      if ($book) {
        $this->title = $book->title;
        $this->author = $book->author;
        $this->category_id = (string) $book->category_id;
        $this->is_public = (bool) $book->is_public;
      }
    }
  }

  public function deleteBook()
  {
    if (!$this->bookToDelete)
      return;

    // Delete the file from storage
    $filePath = public_path('assets/' . $this->bookToDelete->file);
    if (file_exists($filePath)) {
      unlink($filePath);
    }

    // Delete the thumbnail if it exists
    $thumbnailFilename = str_replace('.pdf', '.jpg', $this->bookToDelete->file);
    $thumbnailPath = public_path('book-thumbnails/' . $thumbnailFilename);
    if (file_exists($thumbnailPath)) {
      unlink($thumbnailPath);
    }

    // Delete reviews
    $this->bookToDelete->reviews()->delete();

    // Delete the book
    $bookTitle = $this->bookToDelete->title;
    $bookId = $this->bookToDelete->id;
    $this->bookToDelete->delete();

    // Log the deletion
    app(AuditLogService::class)->log(
      "Deleted book",
      "book",
      "Deleted book",
      $bookId,
      $bookTitle
    );

    $this->confirmingBookDeletion = false;
    $this->bookToDelete = null;

    $this->dispatch(
      'alert',
      [
        'type' => 'success',
        'message' => 'Book deleted successfully!'
      ]
    );
  }

  public function getBooks()
  {
    // Start with a clean query to prevent caching
    $query = Product::query()->withAvg('reviews', 'review_score');

    // Apply search filter
    if ($this->search) {
      $query->where('title', 'like', '%' . $this->search . '%');
    }

    // Apply genre filter
    if (!empty($this->selectedGenres)) {
      $query->whereHas('category', function ($q) {
        $q->whereIn('name', $this->selectedGenres);
      });
    }

    // Apply visibility filter
    if ($this->visibility === 'public') {
      $query->where('is_public', true);
    } elseif ($this->visibility === 'private') {
      $query->where('is_public', false);
    }

    // Apply sorting
    switch ($this->sort) {
      case 'oldest':
        $query->orderBy('created_at', 'asc');
        break;
      case 'title_asc':
        $query->orderBy('title', 'asc');
        break;
      case 'title_desc':
        $query->orderBy('title', 'desc');
        break;
      case 'author_asc':
        $query->orderBy('author', 'asc');
        break;
      case 'author_desc':
        $query->orderBy('author', 'desc');
        break;
      case 'rating_asc':
        $query->orderBy('reviews_avg_review_score', 'asc');
        break;
      case 'rating_desc':
        $query->orderBy('reviews_avg_review_score', 'desc');
        break;
      default: // 'newest'
        $query->orderBy('created_at', 'desc');
    }

    $books = $query->paginate(15);

    // Calculate rating for each book
    $books->each(function ($book) {
      $book->rating = $book->reviews_avg_review_score ?? 0;
    });

    return $books;
  }

  public function render()
  {
    // If we're showing the edit modal but don't have book data, try to load it
    if ($this->showEditModal && $this->editingBook && (empty($this->title) || empty($this->author))) {
      $book = null;
      if (is_object($this->editingBook)) {
        $book = $this->editingBook;
      } else {
        try {
          $book = Product::find($this->editingBook);
        } catch (\Exception $e) {
          // Ignore errors
        }
      }

      if ($book) {
        $this->title = $book->title;
        $this->author = $book->author;
        $this->category_id = (string) $book->category_id;
        $this->is_public = (bool) $book->is_public;
      }
    }

    // Always get fresh book data 
    $books = $this->getBooks();
    $categories = Category::all();

    return view('livewire.books.book-management', [
      'books' => $books,
      'categories' => $categories,
      'totalBooks' => $books->total() // Use total() instead of count()
    ]);
  }
}