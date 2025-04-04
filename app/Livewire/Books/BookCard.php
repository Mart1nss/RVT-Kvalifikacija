<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class BookCard extends Component
{
  public $book;
  public $bookId;
  public $showAdminActions = false;
  public $source = 'library';
  public $isInReadLater = false;
  public $isLoading = false;

  protected $listeners = ['refreshBooks' => 'refreshBookData'];

  public function mount($book, $showAdminActions = false, $source = 'library')
  {
    $this->book = $book;
    $this->bookId = $book->id;
    $this->showAdminActions = $showAdminActions;
    $this->source = $source;

    // Check if book is in read later list
    if (Auth::check()) {
      $this->isInReadLater = $book->isInReadLaterOf(Auth::user());
    }
  }

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
      // Remove from read later
      $this->book->readLater()->where('user_id', $user->id)->delete();
      $message = 'Book removed from read later list';
    } else {
      // Add to read later
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

    // Refresh parent component
    $this->dispatch('refreshBooks');
  }

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

    // Refresh parent component
    $this->dispatch('refreshBooks');
  }

  public function confirmDelete()
  {
    // Send book ID to parent
    $this->dispatch('confirmBookDeletion', ['bookId' => $this->book->id]);
  }

  public function editBook()
  {
    if ($this->book && $this->book->id) {
      // Just dispatch the event to the parent component
      $this->dispatch('editBook', ['bookId' => $this->book->id]);
    }
  }

  public function refreshBookData()
  {
    if ($this->bookId) {
      // Refresh book from database to get latest data
      $this->book = Product::find($this->bookId);
    }
  }

  public function render()
  {
    return view('livewire.books.book-card');
  }
}