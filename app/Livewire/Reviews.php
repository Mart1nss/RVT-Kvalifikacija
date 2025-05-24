<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

/**
 * Atsauksmju komponente
 * 
 * Šī komponente nodrošina grāmatu atsauksmju attēlošanu, pievienošanu un dzēšanu
 * Ļauj lietotājiem novērtēt grāmatas un kārtot atsauksmes pēc dažādiem kritērijiem
 */
class Reviews extends Component
{
  public $product;
  public $review_text = '';
  public $review_score;
  public $sortOrder = 'default';
  public $deleteReviewId = null;
  public $deleteReviewText = '';
  public $showDeleteModal = false;

  protected $rules = [
    'review_text' => 'required|max:250',
    'review_score' => 'required|integer|between:1,5',
  ];

  protected $messages = [
    'review_score.required' => 'Please select a rating',
  ];

  /**
   * Inicializē komponenti ar grāmatas datiem
   * 
   * @param Product
   */
  public function mount(Product $product)
  {
    $this->product = $product;
  }

  /**
   * Renderē komponentes skatu
   * 
   * @return \Illuminate\View\View
   */
  public function render()
  {
    $reviews = $this->getReviews();
    return view('livewire.reviews', [
      'reviews' => $reviews
    ]);
  }

  /**
   * Iegūst sakārtotas atsauksmes atbilstoši izvēlētajai kārtošanai
   * 
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getReviews()
  {
    $reviewsQuery = $this->product->reviews()->with('user');

    switch ($this->sortOrder) {
      case 'highest':
        $reviewsQuery->orderBy('review_score', 'desc');
        break;
      case 'lowest':
        $reviewsQuery->orderBy('review_score', 'asc');
        break;
      case 'newest':
        $reviewsQuery->orderBy('updated_at', 'desc');
        break;
      default:
        $reviewsQuery->orderBy('updated_at', 'asc');
        break;
    }

    return $reviewsQuery->get();
  }

  /**
   * Pievieno jaunu atsauksmi grāmatai
   */
  public function addReview()
  {
    $this->validate();

    $existingReview = Review::where('user_id', auth()->id())
      ->where('product_id', $this->product->id)
      ->first();

    if ($existingReview) {
      $this->dispatch('alert', [
        'type' => 'error',
        'message' => 'You have already reviewed this book.'
      ]);

      return;
    }

    Review::create([
      'review_score' => $this->review_score,
      'review_text' => $this->review_text,
      'user_id' => auth()->id(),
      'product_id' => $this->product->id
    ]);

    $this->review_text = '';
    $this->review_score = null;
    $this->resetValidation();

    $this->dispatch('reviewAdded');

    $this->dispatch('alert', [
      'type' => 'success',
      'message' => 'Review added successfully!'
    ]);
  }

  /**
   * Parāda atsauksmes dzēšanas apstiprinājuma dialogu
   * 
   * @param int
   * @param string
   */
  public function confirmDelete($reviewId, $reviewText)
  {
    $this->deleteReviewId = $reviewId;
    $this->deleteReviewText = $reviewText;
    $this->showDeleteModal = true;
  }

  /**
   * Atceļ atsauksmes dzēšanas procesu
   */
  public function cancelDelete()
  {
    $this->reset(['deleteReviewId', 'deleteReviewText', 'showDeleteModal']);
  }

  /**
   * Dzēš atsauksmi pēc apstiprinājuma
   */
  public function deleteReview()
  {
    $review = Review::findOrFail($this->deleteReviewId);

    if (!auth()->user()->isAdmin() && $review->user_id != Auth::id()) {
      $this->dispatch('alert', [
        'type' => 'error',
        'message' => 'You are not authorized to delete this review.'
      ]);
      $this->cancelDelete();
      return;
    }

    $review->delete();
    $this->cancelDelete();

    $this->dispatch('alert', [
      'type' => 'success',
      'message' => 'Review deleted successfully!'
    ]);
  }
}
