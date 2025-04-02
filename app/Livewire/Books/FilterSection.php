<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Category;

class FilterSection extends Component
{
  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';
  public $visibility = 'all';
  public $isAdmin = false;
  public $genres = [];
  public $totalBooks = 0;

  // Query string parameters
  protected $queryString = [
    'search' => ['except' => ''],
    'sort' => ['except' => 'newest'],
    'visibility' => ['except' => 'all']
  ];

  // Sort options
  public $sortOptions = [
    ['value' => 'newest', 'text' => 'Newest'],
    ['value' => 'oldest', 'text' => 'Oldest'],
    ['value' => 'title_asc', 'text' => 'Title (A-Z)'],
    ['value' => 'title_desc', 'text' => 'Title (Z-A)'],
    ['value' => 'author_asc', 'text' => 'Author (A-Z)'],
    ['value' => 'author_desc', 'text' => 'Author (Z-A)'],
    ['value' => 'rating_asc', 'text' => 'Rating Asc'],
    ['value' => 'rating_desc', 'text' => 'Rating Desc']
  ];

  // Visibility options
  public $visibilityOptions = [
    ['value' => 'all', 'text' => 'All Books'],
    ['value' => 'public', 'text' => 'Public Only'],
    ['value' => 'private', 'text' => 'Private Only']
  ];

  public function mount($sort = 'newest', $isAdmin = false, $visibility = 'all', $totalBooks = 0)
  {
    $this->sort = $sort;
    $this->isAdmin = $isAdmin;
    $this->visibility = $visibility;
    $this->totalBooks = $totalBooks;

    // Initialize from query parameters if they exist
    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];

    // Fetch genres
    $this->fetchGenres();
  }

  public function fetchGenres()
  {
    // Get all unique category names
    $this->genres = Category::pluck('name')->toArray();
  }

  public function updatedSearch()
  {
    $this->emitFilterUpdated();
  }

  public function updatedSort()
  {
    $this->emitFilterUpdated();
  }

  public function updatedVisibility()
  {
    $this->emitFilterUpdated();
  }

  public function updatedSelectedGenres()
  {
    $this->emitFilterUpdated();
  }

  public function toggleGenre($genre)
  {
    $index = array_search($genre, $this->selectedGenres);
    if ($index !== false) {
      // Remove genre
      unset($this->selectedGenres[$index]);
      $this->selectedGenres = array_values($this->selectedGenres); // Re-index array
    } else {
      // Add genre
      $this->selectedGenres[] = $genre;
    }

    $this->emitFilterUpdated();
  }

  public function clearAllFilters()
  {
    $this->search = '';
    $this->selectedGenres = [];
    $this->sort = 'newest';
    if ($this->isAdmin) {
      $this->visibility = 'all';
    }

    $this->emitFilterUpdated();
  }

  public function emitFilterUpdated()
  {
    $this->dispatch('filterUpdated', [
      'search' => $this->search,
      'selectedGenres' => $this->selectedGenres,
      'sort' => $this->sort,
      'visibility' => $this->visibility
    ]);
  }

  public function getSortDisplayText()
  {
    $option = collect($this->sortOptions)->firstWhere('value', $this->sort);
    return $option ? $option['text'] : 'Newest';
  }

  public function getVisibilityDisplayText()
  {
    $option = collect($this->visibilityOptions)->firstWhere('value', $this->visibility);
    return $option ? $option['text'] : 'All Books';
  }

  public function hasActiveFilters()
  {
    return !empty($this->selectedGenres) || !empty($this->search) ||
      ($this->isAdmin && $this->visibility !== 'all');
  }

  public function showFilterInfo()
  {
    return $this->hasActiveFilters() || $this->totalBooks > 0;
  }

  public function render()
  {
    return view('livewire.books.filter-section');
  }
}