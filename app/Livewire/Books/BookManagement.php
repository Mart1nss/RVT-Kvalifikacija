<?php

namespace App\Livewire\Books;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditLogService;

class BookManagement extends Component
{
  use WithPagination;
  use WithFileUploads;

  // Properties for book management
  public $title;
  public $author;
  public $category_id;
  public $is_public = true;
  public $file;
  public $showEditModal = false;
  public $confirmingBookDeletion = false;
  public $bookToDelete;
  public $editingBookId;

  // Filter properties
  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';
  public $visibility = 'all';
  public $categories = [];

  // Listeners for events from child components
  protected $listeners = [
    'refreshBooks' => '$refresh',
    'filterUpdated' => 'updateFilters',
    'confirmBookDeletion' => 'showDeleteModal',
    'editBook' => 'showEditModal'
  ];

  public function mount()
  {
    $this->loadCategories();

    // Initialize from query parameters if they exist
    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];
    $this->sort = request()->query('sort', 'newest');
    $this->visibility = request()->query('visibility', 'all');
  }

  public function loadCategories()
  {
    // Load all categories for admin, including private ones
    $this->categories = Category::orderBy('name')->get();
  }

  public function updateFilters($filters)
  {
    $this->search = $filters['search'] ?? '';
    $this->selectedGenres = $filters['selectedGenres'] ?? [];
    $this->sort = $filters['sort'] ?? 'newest';
    $this->visibility = $filters['visibility'] ?? 'all';
    $this->resetPage();
  }

  public function getBooks()
  {
    $query = Product::query()
      ->withAvg('reviews', 'review_score');

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

    // Apply visibility filter (for admin)
    if ($this->visibility !== 'all') {
      $query->where('is_public', $this->visibility === 'public');
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
    $books = $this->getBooks();
    $totalBooks = $books->total();

    // Dispatch the updated count to the filter section
    $this->dispatch('updateTotalBooks', $totalBooks);

    return view('livewire.books.book-management', [
      'books' => $books,
      'totalBooks' => $totalBooks,
      'categories' => $this->categories
    ]);
  }

  public function showDeleteModal($data)
  {
    $this->bookToDelete = Product::find($data['bookId']);
    $this->confirmingBookDeletion = true;
  }

  public function showEditModal($data)
  {
    $book = Product::find($data['bookId']);
    if ($book) {
      $this->editingBookId = $book->id;
      $this->title = $book->title;
      $this->author = $book->author;
      $this->category_id = $book->category_id;
      $this->is_public = $book->is_public;
      $this->showEditModal = true;
    }
  }

  public function resetEditForm()
  {
    $this->reset(['editingBookId', 'title', 'author', 'category_id', 'is_public', 'showEditModal']);
  }

  public function updateBook()
  {
    $this->validate([
      'title' => 'required|string|max:255',
      'author' => 'required|string|max:255',
      'category_id' => 'required|exists:categories,id',
    ]);

    $book = Product::find($this->editingBookId);
    if ($book) {
      $book->update([
        'title' => $this->title,
        'author' => $this->author,
        'category_id' => $this->category_id,
        'is_public' => $this->is_public,
      ]);

      $this->resetEditForm();
      $this->dispatch('alert', [
        'type' => 'success',
        'message' => 'Book updated successfully'
      ]);
    }
  }
}