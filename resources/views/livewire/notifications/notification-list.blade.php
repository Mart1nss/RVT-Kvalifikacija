<div class="notification-list-wrapper">
  <div class="notifications-list">
    <div class="notifications-header-page">
      <h2>Sent Notifications</h2>
      <div class="filter-buttons">
        <div class="select-container">
          <select class="filter-select" wire:model.live="sortType">
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
          </select>
        </div>

        <div class="select-container">
          <select class="filter-select" wire:model.live="recipientFilter">
            <option value="">Filter by recipient</option>
            <option value="all">All Users</option>
            <option value="users">Users Only</option>
            <option value="admins">Admins Only</option>
            <option value="self">Yourself</option>
          </select>
        </div>
      </div>
    </div>

    <div class="filter-info-row" style="{{ $totalCount > 0 ? 'display: flex;' : 'display: none;' }}">
      <div class="filter-info-left">
        <span class="total-count">
          <span>{{ $filteredCount }}</span> notifications
        </span>
        @if ($recipientFilter)
          <div class="active-filters">
            <span class="filter-tag">
              {{ $recipientFilter === 'all'
                  ? 'All Users'
                  : ($recipientFilter === 'users'
                      ? 'Users Only'
                      : ($recipientFilter === 'admins'
                          ? 'Admins Only'
                          : 'Yourself')) }}
            </span>
          </div>
        @endif
      </div>
      <button class="clear-filters-btn" wire:click="clearFilters"
        style="{{ $recipientFilter || $sortType !== 'newest' ? 'display: flex;' : 'display: none;' }}">
        <i class='bx bx-x'></i> Clear Filters
      </button>
    </div>

    <div class="notifications-cards">
      @forelse($notifications as $notification)
        <div class="notification-card" id="notification-{{ $notification->id }}"
          data-timestamp="{{ $notification->created_at }}">
          <div class="notification-content">
            <div class="notification-text-page">{{ $notification->message }}</div>
            <div class="notification-stats">
              <span class="recipient-type">{{ $notification->recipient_type }}</span>
              <span class="read-count">
                {{ $notification->read_count }}/{{ $notification->total_users }} users read
              </span>
            </div>
            <div class="notification-info">
              <span class="notification-time">
                Sent by {{ $notification->sender->name }} {{ $notification->created_at->diffForHumans() }}
              </span>
            </div>
          </div>
          <div class="notification-actions">
            <button type="button" class="delete-btn" wire:click="openDeleteModal('{{ $notification->id }}')"
              title="Delete notification">
              <i class='bx bx-trash'></i>
            </button>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <p>No notifications found for the selected filters.</p>
        </div>
      @endforelse
    </div>

    <div class="pagination-container">
      {{ $notifications->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
      <div class="delete-modal" id="delete-notification-modal">
        <div class="delete-modal-content">
          <div class="delete-modal-header">
            <h3>Delete Notification</h3>
          </div>
          <div class="delete-modal-body">
            <p>Are you sure you want to delete this notification? This will remove it from all users notifications.</p>
          </div>
          <div class="delete-modal-footer">
            <button class="btn btn-secondary" wire:click="closeDeleteModal">Cancel</button>
            <button class="btn btn-danger" wire:click="deleteSentNotification">Delete</button>
          </div>
        </div>
      </div>
    @endif
  </div>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dropdown = document.getElementById('recipient-filter-dropdown');
      const toggleButton = dropdown.querySelector('.dropdown-toggle');

      toggleButton.addEventListener('click', function() {
        dropdown.classList.toggle('open');
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        if (!dropdown.contains(event.target)) {
          dropdown.classList.remove('open');
        }
      });

      // Prevent dropdown from closing when clicking inside
      dropdown.querySelector('.dropdown-menu').addEventListener('click', function(event) {
        event.stopPropagation();
      });

      // Re-initialize when Livewire updates the DOM
      document.addEventListener('livewire:initialized', function() {
        const dropdown = document.getElementById('recipient-filter-dropdown');
        if (dropdown) {
          const toggleButton = dropdown.querySelector('.dropdown-toggle');
          toggleButton.addEventListener('click', function() {
            dropdown.classList.toggle('open');
          });
        }
      });
    });
  </script>
</div>
