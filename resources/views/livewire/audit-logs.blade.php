<div class="main-container">
  <div class="text-container" style="background: transparent; padding: 0;">
    <h1 class="text-container-title">Audit Logs</h1>
  </div>

  <div class="item-container" style="border-radius: 8px;">

    <div class="search-filter-container">

      <!-- Action Type Filter -->
      <div class="sort-dropdown">
        <select wire:model.live="actionType" class="filter-select">
          <option value="all">All Actions</option>
          <option value="book">Book</option>
          <option value="user">User</option>
          <option value="category">Category</option>
          <option value="notification">Notification</option>
        </select>
      </div>

      <!-- Admin Filter -->
      <div class="sort-dropdown">
        <select wire:model.live="adminId" class="filter-select">
          <option value="all">All Admins</option>
          @foreach ($admins as $admin)
            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
          @endforeach
        </select>
      </div>


    </div>

    <!-- Filter Info Row -->
    <div class="filter-info-row">
      <div class="total-count">{{ $logs->total() }} logs found</div>
      <div id="active-filters">
        @if ($actionType !== 'all')
          <div class="filter-tag">
            Action: {{ ucfirst($actionType) }}
          </div>
        @endif
        @if ($adminId !== 'all')
          <div class="filter-tag">
            Admin: {{ $admins->firstWhere('id', $adminId)->name ?? 'Unknown' }}
          </div>
        @endif

      </div>
      <!-- Clear Filters Button - Only show when filters are active -->
      @if ($actionType !== 'all' || $adminId !== 'all')
        <button wire:click="clearFilters" class="clear-filters-btn">
          <i class='bx bx-reset'></i>
          Clear Filters
        </button>
      @endif
    </div>

    <div class="logs-container">
      @foreach ($logs as $log)
        <div
          class="log-entry {{ str_contains($log->description, 'Changed') || str_contains($log->action_type, 'notification') ? 'expandable' : '' }}"
          x-data="{ expanded: false }" @click="expanded = !expanded">
          <div class="log-header">
            <div class="log-content">
              <div class="log-summary">
                <span class="action-type {{ $log->action_type }}">
                  @if ($log->action_type === 'book')
                    <i class='bx bx-book-alt'></i>
                  @elseif($log->action_type === 'user')
                    <i class='bx bx-user'></i>
                  @elseif($log->action_type === 'category')
                    <i class='bx bx-category'></i>
                  @elseif($log->action_type === 'notification')
                    <i class='bx bx-bell'></i>
                  @elseif($log->action_type === 'ticket')
                    <i class='bx bx-support'></i>
                  @endif
                </span>
                <span class="admin-name">{{ $log->admin ? $log->admin->name : 'Unknown Admin' }}</span>
                <span class="action">{{ $log->action }}</span>
                @if ($log->affected_item_name)
                  <span class="affected-item">"{{ $log->affected_item_name }}"</span>
                @endif
              </div>
              <span class="timestamp">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            @if (str_contains($log->description, 'Changed') || str_contains($log->action_type, 'notification'))
              <i class='bx bx-chevron-down accordion-icon' :style="expanded ? 'transform: rotate(180deg)' : ''"></i>
            @endif
          </div>

          @if (str_contains($log->description, 'Changed'))
            <div class="log-details" x-cloak x-show="expanded" x-transition style="max-height: none;">
              <div class="changes-list">
                @foreach (explode(', ', $log->description) as $change)
                  @if (str_starts_with($change, 'Changed'))
                    @php $change = str_replace('Changed ', '', $change); @endphp
                  @endif
                  <div class="change-item">
                    <i class='bx bx-right-arrow-alt'></i>
                    <span>{{ ucfirst($change) }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          @elseif($log->action_type === 'notification')
            <div class="log-details" x-cloak x-show="expanded" x-transition style="max-height: none;">
              <div class="changes-list">
                <div class="change-item">
                  <i class='bx bx-message-rounded-detail'></i>
                  <span>{{ $log->description }}</span>
                </div>
              </div>
            </div>
          @endif
        </div>
      @endforeach

      <div class="pagination-container">
        {{ $logs->links('vendor.pagination.tailwind') }}
      </div>
    </div>
  </div>
</div>
