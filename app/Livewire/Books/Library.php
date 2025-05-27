<?php

namespace App\Livewire\Books;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

/**
 * Bibliotēkas komponente, kas attēlo publiski pieejamās grāmatas
 * Nodrošina grāmatu meklēšanu, filtrēšanu un kārtošanu
 */
class Library extends Component
{
  use WithPagination;

  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';

  protected $listeners = [
    'refreshBooks' => '$refresh',
    'filterUpdated' => 'updateFilters'
  ];

  /**
   * Inicializē komponenti ar sākotnējiem datiem
   */
  public function mount()
  {
    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];
    $this->sort = request()->query('sort', 'newest');
  }

  /**
   * Atjaunina filtrus no filtru komponentes
   * @param array
   */
  public function updateFilters($filters)
  {
    $this->search = $filters['search'] ?? '';
    $this->selectedGenres = $filters['selectedGenres'] ?? [];
    $this->sort = $filters['sort'] ?? 'newest';
    $this->resetPage();
  }

  /**
   * Pārslēdz grāmatas statusu "Lasīt vēlāk" sarakstā
   * @param int
   */
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
      $book->readLater()->where('user_id', $user->id)->delete();
      $message = 'Book removed from read later list';
    } else {
      $book->readLater()->create(['user_id' => $user->id]);
      $message = 'Book added to read later list';
    }

    $this->dispatch('alert', [
      'type' => 'success',
      'message' => $message
    ]);
  }

  /**
   * Iegūst filtrētas un kārtotas grāmatas
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  public function getBooks()
  {
    $query = Product::query()
      ->whereHas('category', function ($q) {
        $q->where('is_public', true);
      })
      ->withAvg('reviews', 'review_score');

    if ($this->search) {
      $query->where(function ($q) {
        $q->where('title', 'like', '%' . $this->search . '%')
          ->orWhere('author', 'like', '%' . $this->search . '%');
      });
    }

    if (!empty($this->selectedGenres)) {
      $query->whereHas('category', function ($q) {
        $q->whereIn('name', $this->selectedGenres);
      });
    }

    // Pielieto kārtošanu
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
      default:
        $query->orderBy('created_at', 'desc');
    }

    $books = $query->paginate(15);

    $books->each(function ($book) {
      $book->rating = $book->reviews_avg_review_score ?? 0;
    });

    return $books;
  }

  /**
   * Renderē komponentes skatu
   * @return \Illuminate\View\View
   */
  public function render()
  {
    $books = $this->getBooks();
    $totalBooks = $books->total();

    $this->dispatch('updateTotalBooks', $totalBooks);

    return view('livewire.books.library', [
      'books' => $books,
      'totalBooks' => $totalBooks
    ]);
  }
}
