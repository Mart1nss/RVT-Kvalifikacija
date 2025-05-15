<div class="notification-dropdown-wrapper" x-data="{
    init() {
            this.setupScrollListener();
        },
        setupScrollListener() {
            const container = this.$refs.notificationContainer;
            if (container) {
                container.addEventListener('scroll', () => {
                    const { scrollTop, scrollHeight, clientHeight } = container;
                    if (scrollTop + clientHeight >= scrollHeight - 100 && @this.hasMoreNotifications) {
                        @this.loadMore();
                    }
                });
            }
        }
}">
  <div class="notification-dropdown">
    <div class="notification-icon" wire:click="toggleDropdown">
      <i class='bx bx-bell'></i>
      @if ($unreadCount > 0)
        <span class="notification-badge">{{ $unreadCount }}</span>
      @endif
    </div>

    @if ($isOpen)
      <div class="notification-panel">
        <div class="notification-header">
          <h3>Notifications</h3>
          <div class="notification-actions">
            <button wire:click="deleteAllNotifications" class="delete-all-btn">
              Clear all
            </button>
          </div>
        </div>

        <div class="notification-container" x-ref="notificationContainer">
          @if ($loading)
            <div class="notification-loading">
              <div class="spinner"></div>
            </div>
          @else
            @if (count($notifications) > 0)
              @foreach ($notifications as $notification)
                <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                  <div class="notification-content">
                    <p class="notification-message">{{ $notification->data['message'] ?? 'No message' }}</p>
                    <span
                      class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>

                    @if (isset($notification->data['ticket_id']))
                      <div class="notification-actions-row">
                        <a href="{{ route('tickets.show', $notification->data['ticket_id']) }}" class="go-to-btn">
                          <i class='bx bx-link-external'></i> Go to ticket
                        </a>
                      </div>
                    @elseif (isset($notification->data['link']))
                      <div class="notification-actions-row">
                        <a href="{{ $notification->data['link'] }}" class="go-to-btn">
                          <i class='bx bx-link-external'></i> View Details
                        </a>
                      </div>
                    @endif
                  </div>
                  <div class="notification-actions">
                    <button wire:click="deleteNotification('{{ $notification->id }}')" class="delete-btn"
                      title="Delete">
                      <i class='bx bx-x'></i>
                    </button>
                  </div>
                </div>
              @endforeach
            @else
              <div class="empty-notifications">
                <p>No notifications</p>
              </div>
            @endif

            @if ($hasMoreNotifications)
              <div class="load-more-container">
                <div class="spinner-small"></div>
                <p>Loading more...</p>
              </div>
            @endif
          @endif
        </div>
      </div>
    @endif
  </div>
</div>
