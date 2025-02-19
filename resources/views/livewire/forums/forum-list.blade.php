<div>

  <div class="item-container" id="filter-section">
    <div class="search-section">
      <input type="text" placeholder="Search forums..." class="reply-textarea" wire:model.live.debounce.300ms="search">
    </div>

    <div class="sort-section">
      <select wire:model.live="sortBy" class="sort-select">
        <option value="latest">Latest</option>
        <option value="oldest">Oldest</option>
      </select>
    </div>
  </div>

  <div class="forum-list">
    @forelse($forums as $forum)
      <div class="forum-item">
        <div class="forum-info-container">
          <a href="{{ route('forums.view', $forum) }}" class="forum-title">
            {{ $forum->title }}
          </a>
          <p class="forum-description">{{ Str::limit($forum->description, 200) }}</p>
        </div>
        <div class="forum-meta">
          <div class="forum-meta-left">
            <span>By {{ $forum->user->name }}</span>
            <span>{{ $forum->created_at->diffForHumans() }}</span>
          </div>
          <div>
            <span>{{ $forum->replies_count }} {{ Str::plural('reply', $forum->replies_count) }}</span>
          </div>
        </div>
      </div>
    @empty
      <div class="no-items-message">
        <p>No forums found.</p>
      </div>
    @endforelse
  </div>

  <div class="pagination">
    {{ $forums->links('vendor.pagination.tailwind') }}
  </div>


  <script>
    document.addEventListener('livewire:initialized', () => {
      console.log('Livewire Forum List Component Initialized');
    });
  </script>
</div>
