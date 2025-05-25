<?php

namespace App\Livewire\Books;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditLogService;

/**
 * Grāmatu pārvaldības komponente
 * Nodrošina grāmatu saraksta attēlošanu, filtrēšanu, kārtošanu, 
 * kā arī grāmatu pievienošanu, rediģēšanu un dzēšanu
 */
class BookManagement extends Component
{
  use WithPagination;
  use WithFileUploads;

  // Grāmatas pārvaldības īpašības
  public $title;
  public $author;
  public $category_id;
  public $file;
  public $showEditModal = false;
  public $confirmingBookDeletion = false;
  public $bookToDelete;
  public $editingBookId;

  // Filtrēšanas īpašības
  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';
  public $visibility = 'all';
  public $categories = [];

  protected $listeners = [
    'refreshBooks' => '$refresh',
    'filterUpdated' => 'updateFilters',
    'confirmBookDeletion' => 'showDeleteModal',
    'editBook' => 'showEditModal'
  ];

  /**
   * Inicializē komponenti ar sākotnējiem datiem
   */
  public function mount()
  {
    $this->loadCategories();
    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];
    $this->sort = request()->query('sort', 'newest');
    $this->visibility = request()->query('visibility', 'all');
  }

  /**
   * Ielādē visas kategorijas
   */
  public function loadCategories()
  {
    $this->categories = Category::orderBy('name')->get();
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
    $this->visibility = $filters['visibility'] ?? 'all';
    $this->resetPage();
  }

  /**
   * Iegūst filtrētas un kārtotas grāmatas
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  public function getBooks()
  {
    $query = Product::query()
      ->withAvg('reviews', 'review_score');

    if ($this->search) {
      $query->where('title', 'like', '%' . $this->search . '%');
    }

    if (!empty($this->selectedGenres)) {
      $query->whereHas('category', function ($q) {
        $q->whereIn('name', $this->selectedGenres);
      });
    }

    if ($this->visibility !== 'all') {
      $query->whereHas('category', function ($q) {
        $q->where('is_public', $this->visibility === 'public');
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

    // Pievieno vērtējuma datus katrai grāmatai
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

    return view('livewire.books.book-management', [
      'books' => $books,
      'totalBooks' => $totalBooks,
      'categories' => $this->categories
    ]);
  }

  /**
   * Parāda grāmatas dzēšanas apstiprinājuma modālo logu
   * @param array
   */
  public function showDeleteModal($data)
  {
    $this->bookToDelete = Product::find($data['bookId']);
    $this->confirmingBookDeletion = true;
  }

  /**
   * Parāda grāmatas rediģēšanas modālo logu
   * @param array
   */
  public function showEditModal($data)
  {
    $book = Product::find($data['bookId']);
    if ($book) {
      $this->editingBookId = $book->id;
      $this->title = $book->title;
      $this->author = $book->author;
      $this->category_id = $book->category_id;
      $this->showEditModal = true;
    }
  }

  /**
   * Atiestata rediģēšanas formas lauku vērtības
   */
  public function resetEditForm()
  {
    $this->reset(['editingBookId', 'title', 'author', 'category_id', 'showEditModal']);
  }

  /**
   * Dzēš izvēlēto grāmatu
   */
  public function deleteBook()
  {
    if (!$this->bookToDelete) {
      return;
    }

    // Iegūst grāmatas informāciju auditam un sīktēla dzēšanai
    $bookId = $this->bookToDelete->id;
    $bookTitle = $this->bookToDelete->title;
    $bookAuthor = $this->bookToDelete->author;
    $bookFile = $this->bookToDelete->file;

    // Saglabā grāmatas informāciju piezīmēs pirms dzēšanas
    $this->bookToDelete->notes()->update([
      'book_title' => $bookTitle,
      'book_author' => $bookAuthor
    ]);

    // Dzēš grāmatu no glabātuves
    if ($bookFile && Storage::exists('books/' . $bookFile)) {
      Storage::delete('books/' . $bookFile);
    }

    // Dzēš sīktēlu, ja tas eksistē
    $thumbnailFilename = str_replace('.pdf', '.jpg', $bookFile);
    $thumbnailPath = public_path('book-thumbnails/' . $thumbnailFilename);
    if (file_exists($thumbnailPath)) {
      unlink($thumbnailPath);
    }

    // Dzēš grāmatas ierakstu
    $this->bookToDelete->delete();

    // Reģistrē dzēšanu audita žurnālā
    app(AuditLogService::class)->log(
      "Deleted book",
      "book",
      "Deleted book: {$bookTitle} by {$bookAuthor}",
      $bookId,
      "{$bookTitle} by {$bookAuthor}"
    );

    // Atiestata un parāda veiksmīgu ziņojumu
    $this->confirmingBookDeletion = false;
    $this->bookToDelete = null;

    $this->dispatch('alert', [
      'type' => 'success',
      'message' => 'Book deleted successfully'
    ]);
  }

  /**
   * Atjaunina grāmatas informāciju
   */
  public function updateBook()
  {
    $this->validate([
      'title' => 'required|string|max:100',
      'author' => 'required|string|max:50',
      'category_id' => 'required|exists:categories,id',
    ]);

    // Check if a book with the same title and author already exists (excluding the current book)
    $existingBook = Product::where('title', $this->title)
                             ->where('author', $this->author)
                             ->where('id', '!=', $this->editingBookId)
                             ->first();

    if ($existingBook) {
        $this->addError('title', 'A book with this title and author already exists.');
        $this->addError('author', 'A book with this title and author already exists.');
        return;
    }

    $book = Product::find($this->editingBookId);
    if ($book) {
      $originalTitle = $book->title;
      $originalAuthor = $book->author;
      $originalCategoryId = $book->category_id;

      $book->update([
        'title' => $this->title,
        'author' => $this->author,
        'category_id' => $this->category_id,
      ]);

      // Sagatavo izmaiņu aprakstu audita žurnālam
      $changes = [];
      if ($originalTitle !== $this->title) {
        $changes[] = "Title changed from '{$originalTitle}' to '{$this->title}'";
      }
      if ($originalAuthor !== $this->author) {
        $changes[] = "Author changed from '{$originalAuthor}' to '{$this->author}'";
      }
      if ($originalCategoryId !== $this->category_id) {
        $oldCategory = Category::find($originalCategoryId)?->name ?? 'Unknown';
        $newCategory = Category::find($this->category_id)?->name ?? 'Unknown';
        $changes[] = "Category changed from '{$oldCategory}' to '{$newCategory}'";
      }

      if (!empty($changes)) {
        $description = implode(", ", $changes);
        app(AuditLogService::class)->log(
          "Updated book",
          "book",
          $description,
          $book->id,
          $book->title
        );
      }

      $this->resetEditForm();
      $this->dispatch('alert', [
        'type' => 'success',
        'message' => 'Book updated successfully'
      ]);
    }
  }
}
