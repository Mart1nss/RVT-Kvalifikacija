@foreach ($notifications as $notification)
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
      <button type="button" class="delete-btn" onclick="openDeleteModal('{{ $notification->id }}')"
        title="Delete notification">
        <i class='bx bx-trash'></i>
      </button>
    </div>
  </div>
@endforeach
