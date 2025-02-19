<div class="item-container" style="border-radius: 8px;" wire:poll.30s x-data="{ reviewOptionsOpen: null }">
  <!-- Forum Details -->
  <div class="forum-details">
    <h1 class="forum-heading">{{ $forum->title }}</h1>
    <div class="forum-info">
      <span>Posted by {{ $forum->user->name }}</span>
      <span>{{ $forum->created_at->diffForHumans() }}</span>
    </div>
    <div class="forum-description-container">
      <p class="view-forum-description">{{ $forum->description }}</p>
    </div>

    <!-- Replies Section -->
    <div class="replies-section">
      <h2 class="replies-heading">Replies</h2>

      <!-- Reply Form -->
      <div class="reply-form-container">
        <form wire:submit.prevent="addReply" class="reply-form">
          <div style="width: 100%;">
            <div class="input-wrapper">
              <textarea id="replyContent" wire:model="newReply" class="reply-textarea @error('newReply') error @enderror"
                placeholder="Write your reply..." rows="4" maxlength="250"></textarea>
              <span class="char-counter" id="replyCounter">0/250</span>
            </div>
            @error('newReply')
              <p class="error-message" style="color: #EF4444; margin-top: 0.5rem; font-size: 0.875rem;">
                {{ $message }}</p>
            @enderror
          </div>

          <div>
            <button type="submit" class="btn btn-primary btn-md" wire:loading.attr="disabled">
              <span wire:loading.remove wire:target="addReply">Post Reply</span>
              <span wire:loading wire:target="addReply">Posting...</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Sort Replies -->
      <select wire:model.live="sortRepliesBy" class="sort-select-view">
        <option value="latest">Latest First</option>
        <option value="oldest">Oldest First</option>
      </select>

      <!-- Replies List -->
      <div class="replies-list">
        @forelse($replies as $reply)
          <div class="reply-item">
            <div class="reply-header">
              <span class="reply-author">{{ $reply->user->name }}</span>
              <span class="reply-date">{{ $reply->created_at->diffForHumans() }}</span>
              @if (auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->usertype === 'admin'))
                <div class="review-options">
                  <button class="review-options-btn"
                    @click.stop="reviewOptionsOpen = reviewOptionsOpen === {{ $reply->id }} ? null : {{ $reply->id }}">
                    <i class='bx bx-dots-vertical-rounded'></i>
                  </button>
                  <div class="review-options-dropdown" :class="{ 'show': reviewOptionsOpen === {{ $reply->id }} }">
                    <button type="button" wire:click="deleteReply({{ $reply->id }})">
                      <i class='bx bx-trash'></i>
                      Delete<span
                        x-show="{{ auth()->user()->usertype === 'admin' }} && {{ $reply->user_id }} !== {{ auth()->id() }}"></span>
                    </button>
                  </div>
                </div>
              @endif
            </div>
            <p>{{ $reply->content }}</p>
          </div>
        @empty
          <div class="no-items-message">
            <p>No replies yet. Be the first to reply!</p>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="pagination">
        {{ $replies->links('vendor.pagination.tailwind') }}
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const replyInput = document.getElementById('replyContent');
      const replyCounter = document.getElementById('replyCounter');
      const maxLength = 250;

      function updateCounter(input, counter, maxLength) {
        const count = input?.value?.length || 0;
        counter.textContent = `${count}/${maxLength}`;

        if (count >= maxLength) {
          counter.classList.add('at-limit');
          counter.classList.remove('near-limit');
        } else if (count >= maxLength * 0.8) {
          counter.classList.add('near-limit');
          counter.classList.remove('at-limit');
        } else {
          counter.classList.remove('near-limit', 'at-limit');
        }
      }

      if (replyInput && replyCounter) {
        replyInput.addEventListener('input', () => updateCounter(replyInput, replyCounter, maxLength));
        updateCounter(replyInput, replyCounter, maxLength);
      }

      // Update counter when Livewire updates the DOM
      document.addEventListener('livewire:initialized', function() {
        if (replyInput && replyCounter) {
          updateCounter(replyInput, replyCounter, maxLength);
        }
      });
    });
  </script>
</div>
