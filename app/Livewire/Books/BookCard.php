<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

/**
 * Grāmatu kartiņas komponente, kas attēlo vienu grāmatu sarakstā
 * Un nodrošina ar to saistītās darbības
 */
class BookCard extends Component
{
  public $book;
  public $bookId;
  public $showAdminActions = false;
  public $source = 'library';
  public $isInReadLater = false;
  public $isLoading = false;

  protected $listeners = ['refreshBooks' => 'refreshBookData'];

  /**
   * 
   * @param object
   * @param bool
   * @param string
   */
  public function mount($book, $showAdminActions = false, $source = 'library')
  {
    $this->book = $book;
    $this->bookId = $book->id;
    $this->showAdminActions = $showAdminActions;
    $this->source = $source;

    // Pārbauda, vai grāmata ir lietotāja "Lasīt vēlāk" sarakstā
    if (Auth::check()) {
      $this->isInReadLater = $book->isInReadLaterOf(Auth::user());
    }
  }

  /**
   * Pārslēdz grāmatas statusu "Lasīt vēlāk" sarakstā
   */
  public function toggleReadLater()
  {
    if (!Auth::check()) {
      $this->dispatch(
        'alert',
        [
          'type' => 'error',
          'message' => 'Please login to add books to your read later list'
        ]
      );
      return;
    }

    $this->isLoading = true;

    $user = Auth::user();

    if ($this->isInReadLater) {
      $this->book->readLater()->where('user_id', $user->id)->delete();
      $message = 'Book removed from read later list';
    } else {
      $this->book->readLater()->create(['user_id' => $user->id]);
      $message = 'Book added to read later list';
    }

    $this->isInReadLater = !$this->isInReadLater;
    $this->isLoading = false;

    $this->dispatch(
      'alert',
      [
        'type' => 'success',
        'message' => $message
      ]
    );
  }

  /**
   * Dzēš grāmatu no "Lasīt vēlāk" saraksta
   */
  public function deleteFromReadLater()
  {
    if (!Auth::check())
      return;

    $this->book->readLater()->where('user_id', Auth::id())->delete();

    $this->dispatch(
      'alert',
      [
        'type' => 'success',
        'message' => 'Book removed from read later list'
      ]
    );

    $this->dispatch('refreshBooks');
  }

  /**
   * Dzēš grāmatu no izlases saraksta
   */
  public function deleteFromFavorites()
  {
    if (!Auth::check())
      return;

    $this->book->favorites()->where('user_id', Auth::id())->delete();

    $this->dispatch(
      'alert',
      [
        'type' => 'success',
        'message' => 'Book removed from favorites'
      ]
    );

    $this->dispatch('refreshBooks');
  }

  /**
   * Apstiprina grāmatas dzēšanu
   */
  public function confirmDelete()
  {
    $this->dispatch('confirmBookDeletion', ['bookId' => $this->book->id]);
  }

  /**
   * Pāriet uz grāmatas rediģēšanas skatu
   */
  public function editBook()
  {
    if ($this->book && $this->book->id) {
      $this->dispatch('editBook', ['bookId' => $this->book->id]);
    }
  }

  /**
   * Atsvaidzina grāmatas datus
   */
  public function refreshBookData()
  {
    if ($this->bookId) {
      $this->book = Product::find($this->bookId);
    }
  }

  /**
   * Renderē komponentes skatu
   */
  public function render()
  {
    return view('livewire.books.book-card');
  }
}