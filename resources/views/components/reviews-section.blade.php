<link rel="stylesheet" href="{{ asset('css/components/review-section.css') }}">

<div id="reviews-div" class="reviews-div" x-data="{
    reviews: @js(
        $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'review_score' => $review->review_score,
                'review_text' => $review->review_text,
                'user_id' => $review->user_id,
                'user' => [
                    'name' => $review->user->name,
                ],
                'updated_at' => $review->updated_at,
                'timeAgo' => '',
            ];
        })
    ),
    sortOrder: 'default',
    sortOptions: {
        default: 'Default',
        highest: 'Rating Desc',
        lowest: 'Rating Asc',
        newest: 'Newest'
    },
    dropdownOpen: false,
    reviewOptionsOpen: null,
    charCount: 0,
    maxChars: 250,
    reviewText: '',
    selectedRating: null,
    showRatingError: false,
    deleteReviewId: null,
    deleteReviewText: '',
    showDeleteModal: false,

    init() {
        this.$watch('reviewText', (value) => {
            this.charCount = value.length;
        });

        this.updateTimestamps();
        setInterval(() => this.updateTimestamps(), 60000);

        // Add scroll detection for sort options
        this.$nextTick(() => {
            const sortContent = document.querySelector('.sort-dropdown .dropdown-content');
            if (sortContent) {
                sortContent.addEventListener('scroll', () => {
                    const isScrolledToRight = sortContent.scrollLeft + sortContent.clientWidth >= sortContent.scrollWidth - 10;
                    const isScrolledToLeft = sortContent.scrollLeft <= 10;

                    if (isScrolledToRight) {
                        sortContent.classList.add('scrolled-right');
                        sortContent.classList.remove('scrolled-middle');
                    } else if (isScrolledToLeft) {
                        sortContent.classList.remove('scrolled-right', 'scrolled-middle');
                    } else {
                        sortContent.classList.add('scrolled-middle');
                        sortContent.classList.remove('scrolled-right');
                    }
                });
            }
        });
    },

    updateTimestamps() {
        this.reviews.forEach(review => {
            review.timeAgo = moment(review.updated_at).fromNow();
        });
    },

    sortReviews(order) {
        this.sortOrder = order;
        this.dropdownOpen = false;

        const sortFunctions = {
            default: (a, b) => new Date(a.updated_at) - new Date(b.updated_at),
            highest: (a, b) => b.review_score - a.review_score,
            lowest: (a, b) => a.review_score - b.review_score,
            newest: (a, b) => new Date(b.updated_at) - new Date(a.updated_at)
        };

        this.reviews = [...this.reviews].sort(sortFunctions[order]);
    },

    submitReview() {
        if (!this.selectedRating) {
            this.showRatingError = true;
            setTimeout(() => this.showRatingError = false, 3000);
            return false;
        }
        return true;
    },

    closeAllDropdowns(event) {
        if (!event.target.closest('.dropdown') && !event.target.closest('.review-options')) {
            this.dropdownOpen = false;
            this.reviewOptionsOpen = null;
        }
    },

    confirmDelete(reviewId, reviewText) {
        this.deleteReviewId = reviewId;
        this.deleteReviewText = reviewText;
        this.showDeleteModal = true;
        this.reviewOptionsOpen = null;
    },

    cancelDelete() {
        this.showDeleteModal = false;
        this.deleteReviewId = null;
        this.deleteReviewText = '';
    },

    submitDelete() {
        if (this.deleteReviewId) {
            document.querySelector(`#delete-form-${this.deleteReviewId}`).submit();
        }
    }
}" @click.away="closeAllDropdowns($event)">
  <h1 class="reviews-header">REVIEWS</h1>
  @auth
    <form class="review-form" method="POST" action="{{ route('products.reviews.store', $product->id) }}" id="reviewForm"
      @submit.prevent="submitReview() && $el.submit()">
      @csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <div class="rating-container">
        <div class="star-rating-header">
          <span class="rating-error" :class="{ 'show': showRatingError }" id="ratingError">
            Please select a rating
          </span>
        </div>
        <div class="star-rating">
          @for ($i = 5; $i >= 1; $i--)
            <input type="radio" id="rating-{{ $i }}" name="review_score" value="{{ $i }}"
              x-model="selectedRating">
            <label for="rating-{{ $i }}" class="star">
              <i class='bx bxs-star'></i>
            </label>
          @endfor
        </div>
      </div>
      <div class="review-input-button">
        <textarea class="review-textbox" name="review_text" placeholder="Write your review here..." required maxlength="250"
          x-model="reviewText" x-ref="reviewTextarea"
          @input="$refs.reviewTextarea.style.height = '40px'; $refs.reviewTextarea.style.height = $refs.reviewTextarea.scrollHeight + 'px'"></textarea>
        <div class="char-count" :class="{ 'text-danger': charCount >= maxChars }">
          <span x-text="charCount"></span> / <span x-text="maxChars"></span>
        </div>
        <button class="button-review" type="submit">Submit</button>
      </div>
    </form>
  @else
    <p><a href="{{ route('login') }}">Login</a> to add a review.</p>
  @endauth

  <h3
    style="margin-bottom: 8px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 18px;">
    Sort by
  </h3>
  <div class="filter-buttons">
    <div class="sort-dropdown">
      <button class="dropdown-btn" @click.stop="dropdownOpen = !dropdownOpen">
        <i class='bx bx-sort-alt-2'></i>
        <span x-text="sortOptions[sortOrder]"></span>
      </button>
      <div class="dropdown-content" :class="{ 'show': dropdownOpen }">
        <template x-for="(label, value) in sortOptions" :key="value">
          <a href="#" @click.prevent="sortReviews(value)" :class="{ 'active': sortOrder === value }"
            x-text="label"></a>
        </template>
      </div>
    </div>
  </div>

  <div id="reviews-container">
    @if ($reviews->isEmpty())
      <div class="no-reviews">
        <i class='bx bx-message-square-dots'></i>
        <p>There are no reviews yet.</p>
        <p>Be the first to share your thoughts!</p>
      </div>
    @else
      <template x-for="review in reviews" :key="review.id">
        <div class="review-card" :data-score="review.review_score">
          @if (auth()->check())
            <template
              x-if="review.user_id === {{ auth()->id() }} || {{ auth()->user()->isAdmin() ? 'true' : 'false' }}">
              <div class="review-options">
                <button class="review-options-btn"
                  @click.stop="reviewOptionsOpen = reviewOptionsOpen === review.id ? null : review.id">
                  <i class='bx bx-dots-vertical-rounded'></i>
                </button>
                <div class="review-options-dropdown" :class="{ 'show': reviewOptionsOpen === review.id }">
                  <form :id="'delete-form-' + review.id" :action="'/products/{{ $product->id }}/reviews/' + review.id"
                    method="POST" @submit.prevent="confirmDelete(review.id, review.review_text)">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="confirmDelete(review.id, review.review_text)">
                      <i class='bx bx-trash'></i>
                      Delete<span x-show="{{ auth()->user()->isAdmin() }} && review.user_id !== {{ auth()->id() }}">
                        (Admin)</span>
                    </button>
                  </form>
                </div>
              </div>
            </template>
          @endif
          <p class="reviewed-by" x-text="review.user.name"></p>
          <div class="star-rating">
            <template x-for="i in 5" :key="i">
              <span class="star" :class="{ 'filled': i <= review.review_score }">
                <i class='bx bxs-star'></i>
              </span>
            </template>
          </div>
          <p style="margin-bottom: 10px;" x-text="review.review_text"></p>
          <span class="last-updated">
            <em x-text="review.timeAgo"></em>
          </span>
        </div>
      </template>
    @endif
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="delete-modal" :class="{ 'show': showDeleteModal }" @click.self="cancelDelete()">
    <div class="delete-modal-content">
      <div class="delete-modal-header">Delete Review</div>
      <div class="delete-modal-body">
        Are you sure you want to delete this review?
        <br><br>
        <em x-text="deleteReviewText"></em>
      </div>
      <div class="delete-modal-footer">
        <button class="delete-modal-btn cancel" @click="cancelDelete()">Cancel</button>
        <button class="delete-modal-btn confirm" @click="submitDelete()">Delete</button>
      </div>
    </div>
  </div>
</div>
