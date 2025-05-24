<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Category;

/**
 * Filtru sekcijas komponente grāmatu meklēšanai un filtrēšanai
 * Nodrošina meklēšanu, kārtošanu un filtrēšanu pēc žanriem un redzamības
 */
class FilterSection extends Component
{
  public $search = '';
  public $selectedGenres = [];
  public $sort = 'newest';
  public $visibility = 'all';
  public $isAdmin = false;
  public $genres = [];
  public $totalBooks = 0;

  protected $listeners = ['updateTotalBooks'];

  protected $queryString = [
    'search' => ['except' => ''],
    'sort' => ['except' => 'newest'],
    'visibility' => ['except' => 'all']
  ];

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

  public $visibilityOptions = [
    ['value' => 'all', 'text' => 'All Books'],
    ['value' => 'public', 'text' => 'Public Only'],
    ['value' => 'private', 'text' => 'Private Only']
  ];

  /**
   * Inicializē komponenti ar sākotnējiem datiem
   * @param string
   * @param bool
   * @param string
   * @param int
   */
  public function mount($sort = 'newest', $isAdmin = false, $visibility = 'all', $totalBooks = 0)
  {
    $this->sort = $sort;
    $this->isAdmin = $isAdmin;
    $this->visibility = $visibility;
    $this->totalBooks = $totalBooks;

    $this->search = request()->query('query', '');
    $this->selectedGenres = request()->query('genres') ? explode(',', request()->query('genres')) : [];

    $this->fetchGenres();
  }

  /**
   * Ielādē žanru sarakstu no datu bāzes
   */
  public function fetchGenres()
  {
    $query = Category::query();

    if (!$this->isAdmin) {
      $query->where('is_public', true);
    }

    $this->genres = $query->pluck('name')->toArray();
  }

  /**
   * Notiek, kad tiek atjaunināts meklēšanas vaicājums
   */
  public function updatedSearch()
  {
    $this->emitFilterUpdated();
  }

  /**
   * Notiek, kad tiek atjaunināts kārtošanas parametrs
   */
  public function updatedSort()
  {
    $this->emitFilterUpdated();
  }

  /**
   * Notiek, kad tiek atjaunināts redzamības filtrs
   */
  public function updatedVisibility()
  {
    $this->emitFilterUpdated();
  }

  /**
   * Notiek, kad tiek atjaunināti atlasītie žanri
   */
  public function updatedSelectedGenres()
  {
    $this->emitFilterUpdated();
  }

  /**
   * Pārslēdz žanra atlasi filtrēšanai
   * @param string
   */
  public function toggleGenre($genre)
  {
    $index = array_search($genre, $this->selectedGenres);
    if ($index !== false) {
      unset($this->selectedGenres[$index]);
      $this->selectedGenres = array_values($this->selectedGenres);
    } else {
      $this->selectedGenres[] = $genre;
    }

    $this->emitFilterUpdated();
  }

  /**
   * Notīra visus filtrus un atgriež sākotnējos iestatījumus
   */
  public function clearAllFilters()
  {
    $this->reset(['search', 'selectedGenres', 'sort']);
    if ($this->isAdmin) {
      $this->reset('visibility');
    }

    $this->dispatch('filter-cleared');

    $this->emitFilterUpdated();
  }

  /**
   * Nosūta filtru atjaunināšanas notikumu vecāka komponentei
   */
  public function emitFilterUpdated()
  {
    $this->dispatch('filterUpdated', [
      'search' => $this->search,
      'selectedGenres' => $this->selectedGenres,
      'sort' => $this->sort,
      'visibility' => $this->visibility
    ]);
  }

  /**
   * Atjaunina kopējo grāmatu skaitu
   * @param int
   */
  public function updateTotalBooks($count)
  {
    $this->totalBooks = $count;
  }

  /**
   * Iegūst pašreizējās kārtošanas teksta attēlojumu
   * @return string
   */
  public function getSortDisplayText()
  {
    $option = collect($this->sortOptions)->firstWhere('value', $this->sort);
    return $option ? $option['text'] : 'Newest';
  }

  /**
   * Iegūst pašreizējās redzamības teksta attēlojumu
   * @return string
   */
  public function getVisibilityDisplayText()
  {
    $option = collect($this->visibilityOptions)->firstWhere('value', $this->visibility);
    return $option ? $option['text'] : 'All Books';
  }

  /**
   * Pārbauda, vai ir aktīvi filtri
   * @return bool
   */
  public function hasActiveFilters()
  {
    return !empty($this->selectedGenres) || !empty($this->search) ||
      ($this->isAdmin && $this->visibility !== 'all');
  }

  /**
   * Nosaka, vai rādīt filtru informāciju
   * @return bool
   */
  public function showFilterInfo()
  {
    return $this->hasActiveFilters() || $this->totalBooks > 0;
  }

  /**
   * Renderē komponentes skatu
   * @return \Illuminate\View\View
   */
  public function render()
  {
    return view('livewire.books.filter-section');
  }
}