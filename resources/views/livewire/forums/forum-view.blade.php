<div class="item-container" style="border-radius: 8px;" wire:poll.30s>
  <!-- Forum Details -->
  <div class="forum-details">
    <div class="forum-header">
      <h1 class="forum-heading">{{ $forum->title }}</h1>
      
      @if (auth()->check() && (auth()->id() === $forum->user_id || auth()->user()->usertype === 'admin'))
        <div class="review-options" x-data="{ optionsOpen: false }" @click.away="optionsOpen = false">
          <button class="review-options-btn" @click.stop="optionsOpen = !optionsOpen">
            <i class='bx bx-dots-vertical-rounded'></i>
          </button>
          <div class="review-options-dropdown" :class="{ 'show': optionsOpen }">
            <button type="button" wire:click="deleteForum">
              <i class='bx bx-trash'></i>
              Delete<span
                x-show="'{{ auth()->user()->usertype }}' === 'admin' && {{ $forum->user_id }} !== {{ auth()->id() }}"></span>
            </button>
          </div>
        </div>
      @endif
    </div>
    <div class="forum-info">
      <span>{{ $forum->user->name }}</span>
      <span>&#8226;</span>
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
                placeholder="Write your reply..." rows="4" maxlength="250" data-counter="replyCounter"></textarea>
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
                <div class="review-options" x-data="{ optionsOpen: false }" @click.away="optionsOpen = false">
                  <button class="review-options-btn" @click.stop="optionsOpen = !optionsOpen">
                    <i class='bx bx-dots-vertical-rounded'></i>
                  </button>
                  <div class="review-options-dropdown" :class="{ 'show': optionsOpen }">
                    <button type="button" wire:click="deleteReply({{ $reply->id }})">
                      <i class='bx bx-trash'></i>
                      Delete<span
                        x-show="'{{ auth()->user()->usertype }}' === 'admin' && {{ $reply->user_id }} !== {{ auth()->id() }}"></span>
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
    // Char counter
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('textarea[data-counter]').forEach(textarea => {
        const counterId = textarea.dataset.counter;
        const counter = document.getElementById(counterId);
        const maxLength = textarea.getAttribute('maxlength') || 250;
        
        if (!counter) return;
        
        const updateCount = () => {
          const count = textarea.value.length;
          counter.textContent = `${count}/${maxLength}`;
        };
        
        textarea.addEventListener('input', updateCount);
        updateCount();
      });
      
      // Listen for Livewire events
      Livewire.on('reply-added', () => {
        const textarea = document.getElementById('replyContent');
        if (textarea) {
          textarea.value = '';
          const counterId = textarea.dataset.counter;
          const counter = document.getElementById(counterId);
          if (counter) {
            const maxLength = textarea.getAttribute('maxlength') || 250;
            counter.textContent = `0/${maxLength}`;
          }
        }
      });
    });
  </script>
</div>
