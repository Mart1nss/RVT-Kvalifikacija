<?php

namespace App\Livewire\Books;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Library extends Component
{
  use WithPagination;

  // Filter properties
  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';

  // Listeners for events from child components
  protected $listeners = [
    'refreshBooks' => '$refresh',
    'filterUpdated' => 'updateFilters'
  ];

  public function mount()
  {
    // Initialize from query parameters if they exist
    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];
    $this->sort = request()->query('sort', 'newest');
  }

  public function updateFilters($filters)
  {
    $this->search = $filters['search'] ?? '';
    $this->selectedGenres = $filters['selectedGenres'] ?? [];
    $this->sort = $filters['sort'] ?? 'newest';
    $this->resetPage();
  }

  public function toggleReadLater($bookId)
  {
    if (!Auth::check()) {
      $this->dispatch('alert', [
        'type' => 'error',
        'message' => 'Please login to add books to your read later list'
      ]);
      return;
    }

    $book = Product::find($bookId);
    if (!$book)
      return;

    $user = Auth::user();
    $isInReadLater = $book->isInReadLaterOf($user);

    if ($isInReadLater) {
      // Remove from read later
      $book->readLater()->where('user_id', $user->id)->delete();
      $message = 'Book removed from read later list';
    } else {
      // Add to read later
      $book->readLater()->create(['user_id' => $user->id]);
      $message = 'Book added to read later list';
    }

    $this->dispatch('alert', [
      'type' => 'success',
      'message' => $message
    ]);
  }

  public function getBooks()
  {
    $query = Product::query()
      ->where('is_public', true)
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

    return view('livewire.books.library', [
      'books' => $books,
      'totalBooks' => $books->total()
    ]);
  }
}