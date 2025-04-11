<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

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

  public function mount(Product $product)
  {
    $this->product = $product;
  }

  public function render()
  {
    $reviews = $this->getReviews();
    return view('livewire.reviews', [
      'reviews' => $reviews
    ]);
  }

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

  public function addReview()
  {
    $this->validate();

    Review::create([
      'review_score' => $this->review_score,
      'review_text' => $this->review_text,
      'user_id' => auth()->id(),
      'product_id' => $this->product->id
    ]);

    // Reset the form completely
    $this->review_text = '';
    $this->review_score = null;
    $this->resetValidation();

    // Force a re-render to reset the star ratings properly
    $this->dispatch('reviewAdded');

    // Dispatch event for alert system
    $this->dispatch('alert', [
      'type' => 'success',
      'message' => 'Review added successfully!'
    ]);
  }

  public function confirmDelete($reviewId, $reviewText)
  {
    $this->deleteReviewId = $reviewId;
    $this->deleteReviewText = $reviewText;
    $this->showDeleteModal = true;
  }

  public function cancelDelete()
  {
    $this->reset(['deleteReviewId', 'deleteReviewText', 'showDeleteModal']);
  }

  public function deleteReview()
  {
    $review = Review::findOrFail($this->deleteReviewId);

    // Allow admins to delete any review, regular users can only delete their own
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

    // Dispatch event for alert system
    $this->dispatch('alert', [
      'type' => 'success',
      'message' => 'Review deleted successfully!'
    ]);
  }

  public function updatedSortOrder()
  {
    // No additional code needed here as the render method will get the sorted reviews
  }
}