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

                    @if (isset($notification->data['ticket_id']) || isset($notification->data['link']))
                      <div class="notification-actions-row">
                        <button wire:click.prevent="viewNotification('{{ $notification->id }}')" class="go-to-btn">
                          <i class='bx bx-link-external'></i> 
                          @if (isset($notification->data['ticket_id']))
                            Go to ticket
                          @else
                            View Details
                          @endif
                        </button>
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

@script
<script>
  document.addEventListener('livewire:navigated', () => { // Use livewire:navigated for SPA, or DOMContentLoaded for initial full page loads
    // Ensure listeners are set up after Livewire component is initialized or navigated to
    if (typeof Livewire !== 'undefined') {
      Livewire.on('navigateTo', (event) => {
        if (event.url) {
          window.location.href = event.url;
        } else {
          console.error('navigateTo event triggered without a URL', event);
        }
      });

      Livewire.on('showToast', (event) => {
        // Basic alert, replace with a proper toast notification system if available
        alert(event.message || 'An update occurred.'); 
        // Example: if using a global toast function like window.showToast(message, type)
        // if (window.showToast && typeof window.showToast === 'function') {
        //   window.showToast(event.message, event.type || 'info');
        // } else {
        //   alert((event.type ? event.type.toUpperCase() + ': ' : '') + event.message);
        // }
      });
    }
  });

  // Fallback for initial load if livewire:navigated doesn't cover it
  // (though for Livewire 3, livewire:navigated should be sufficient for components within SPA mode)
  if (typeof Livewire !== 'undefined' && !Livewire.all().length) { // Check if Livewire has already initialized components
      document.addEventListener('DOMContentLoaded', () => {
          if (typeof Livewire !== 'undefined') {
              Livewire.on('navigateTo', (event) => {
                  if (event.url) {
                      window.location.href = event.url;
                  } else {
                      console.error('navigateTo event triggered without a URL during DOMContentLoaded', event);
                  }
              });

              Livewire.on('showToast', (event) => {
                  alert(event.message || 'An update occurred on initial load.');
              });
          }
      });
  }
</script>
@endscript
