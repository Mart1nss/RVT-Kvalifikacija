<div id="reviews-div" class="reviews-div">
  <link rel="stylesheet" href="{{ asset('css/components/review-section.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">

  <h1 class="reviews-header">REVIEWS</h1>
  @auth
    <form class="review-form" wire:submit.prevent="addReview" id="reviewForm">
      <div class="rating-container">
        <div class="star-rating-header">
          <span class="rating-error @error('review_score') show @enderror" id="ratingError">
            @error('review_score')
              {{ $message }}
            @else
              Please select a rating
            @enderror
          </span>
        </div>
        <div class="star-rating">
          @for ($i = 5; $i >= 1; $i--)
            <input type="radio" id="rating-{{ $i }}" wire:model.live="review_score" name="review_score"
              value="{{ $i }}">
            <label for="rating-{{ $i }}" class="star">
              <i class='bx bxs-star'></i>
            </label>
          @endfor
        </div>
      </div>
      <div class="review-input-button">
        <textarea id="reviewTextarea" class="review-textbox" wire:model="review_text" placeholder="Write your review here..." required
          maxlength="250" x-data="{}" x-init="$el.style.height = '40px';
          $el.addEventListener('input', function() {
              $el.style.height = '40px';
              $el.style.height = $el.scrollHeight + 'px';
          });
          
          Livewire.on('reviewAdded', () => {
              $el.value = ''; // Clear textarea
              $el.style.height = '40px'; // Reset height
              const event = new Event('input', { bubbles: true, cancelable: true });
              $el.dispatchEvent(event);
          });" wire:loading.class="disabled" wire:target="addReview"></textarea>
        <div id="charCount" class="char-count">0 / 250</div>
        <button class="btn btn-primary btn-sm btn-responsive" type="submit" wire:loading.attr="disabled"
          wire:target="addReview">
          <span wire:loading.remove wire:target="addReview">Submit</span>
          <span wire:loading wire:target="addReview">Submitting...</span>
        </button>
      </div>
    </form>

    <script>
      document.addEventListener('livewire:initialized', () => {
        // Function to update the character counter for reviews
        const updateReviewCharCounter = () => {
          const textarea = document.getElementById('reviewTextarea');
          const charCountDisplay = document.getElementById('charCount');
          
          if (textarea && charCountDisplay) {
            const currentLength = textarea.value.length;
            const maxLength = textarea.getAttribute('maxlength') || 250;
            charCountDisplay.textContent = `${currentLength} / ${maxLength}`;
            
            if (currentLength >= maxLength) {
              charCountDisplay.classList.add('text-danger');
            } else {
              charCountDisplay.classList.remove('text-danger');
            }
          }
        };

        updateReviewCharCounter();

        const reviewTextarea = document.getElementById('reviewTextarea');
        if (reviewTextarea) {
          reviewTextarea.addEventListener('input', updateReviewCharCounter);
        }

        Livewire.on('reviewAdded', () => {
          const stars = document.querySelectorAll('input[name="review_score"]');
          stars.forEach(star => {
            star.checked = false;
          });
        });

        Livewire.hook('message.processed', (message, component) => {
          updateReviewCharCounter();
        });
      });
    </script>
  @else
    <p><a href="{{ route('login') }}">Login</a> to add a review.</p>
  @endauth

  <h3
    style="margin-bottom: 8px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 18px;">
    Sort by
  </h3>
  <div class="filter-buttons">
    <select class="form-control filter-select" wire:model.live="sortOrder">
      <option value="default">Default</option>
      <option value="highest">Rating Desc</option>
      <option value="lowest">Rating Asc</option>
      <option value="newest">Newest</option>
    </select>
  </div>

  <div id="reviews-container">
    @if ($reviews->isEmpty())
      <div class="no-reviews">
        <i class='bx bx-message-square-dots'></i>
        <p>There are no reviews yet.</p>
        <p>Be the first to share your thoughts!</p>
      </div>
    @else
      @foreach ($reviews as $review)
        <div class="review-card" data-score="{{ $review->review_score }}">
          @if (auth()->check())
            @if ($review->user_id === auth()->id() || auth()->user()->isAdmin())
              <div class="review-options" x-data="{ optionsOpen: false }" @click.away="optionsOpen = false">
                <button class="review-options-btn" @click.stop="optionsOpen = !optionsOpen">
                  <i class='bx bx-dots-vertical-rounded'></i>
                </button>
                <div class="review-options-dropdown" :class="{ 'show': optionsOpen }">
                  <button type="button"
                    wire:click="confirmDelete({{ $review->id }}, '{{ addslashes($review->review_text) }}')">
                    <i class='bx bx-trash'></i>
                    Delete
                    @if (auth()->user()->isAdmin() && $review->user_id !== auth()->id())
                      (Admin)
                    @endif
                  </button>
                </div>
              </div>
            @endif
          @endif
          <p class="reviewed-by">{{ $review->user ? $review->user->name : 'Deleted User' }}</p>
          <div class="star-rating">
            @for ($i = 1; $i <= 5; $i++)
              <span class="star {{ $i <= $review->review_score ? 'filled' : '' }}">
                <i class='bx bxs-star'></i>
              </span>
            @endfor
          </div>
          <p style="margin-bottom: 10px;">{{ $review->review_text }}</p>
          <span class="last-updated">
            <em>{{ $review->updated_at->diffForHumans() }}</em>
          </span>
        </div>
      @endforeach
    @endif
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="delete-modal {{ $showDeleteModal ? 'show' : '' }}" wire:click.self="cancelDelete">
    <div class="delete-modal-content">
      <div class="delete-modal-header">Delete Review</div>
      <div class="delete-modal-body">
        Are you sure you want to delete this review?
        <br><br>
        <em>{{ $deleteReviewText }}</em>
      </div>
      <div class="delete-modal-footer">
        <button class="delete-modal-btn cancel" wire:click="cancelDelete">Cancel</button>
        <button class="delete-modal-btn confirm" wire:click="deleteReview">Delete</button>
      </div>
    </div>
  </div>

  <style>
    .disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
    .text-danger {
      color: #dc2626 !important;
    }
  </style>
</div>
