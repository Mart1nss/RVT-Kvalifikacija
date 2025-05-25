<div class="main-container">
  <div class="text-container" style="background: transparent; padding: 0;">
    <h1 class="text-container-title">Audit Logs</h1>
  </div>

  <div class="item-container" style="border-radius: 8px;">

    <div class="search-filter-container">

      <!-- Action Type Filter -->
      <div class="sort-dropdown">
        <select wire:model.live="actionType" class="filter-select" id="actionTypeFilter">
          <option value="all">All Actions</option>
          <option value="book">Book</option>
          <option value="user">User</option>
          <option value="category">Category</option>
          <option value="notification">Notification</option>
        </select>
      </div>

      <!-- Admin Filter -->
      <div class="sort-dropdown">
        <select wire:model.live="adminId" class="filter-select" id="adminIdFilter">
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
        @php
            $isAssignedTicket = ($log->action_type === 'ticket' && $log->action === 'Assigned ticket');
            $parsedTicketId = null;
            $parsedTicketUser = null;

            if ($isAssignedTicket) {
                // Try to get Ticket ID from affected_item_id first as it's more reliable
                if ($log->affected_item_id) {
                    $parsedTicketId = $log->affected_item_id;
                }
                // Fallback to parsing from description if not found in affected_item_id
                if (!$parsedTicketId) {
                    if (preg_match('/Ticket #(\d+)/i', $log->description, $matchesId)) {
                        $parsedTicketId = $matchesId[1];
                    } elseif (preg_match('/Ticket ID: (\d+)/i', $log->description, $matchesId)) {
                        $parsedTicketId = $matchesId[1];
                    }
                }

                if (preg_match('/from user ([\w\s.-]+?)(?: for | assigned| to admin|,|$)/i', $log->description, $matchesUser)) {
                    $parsedTicketUser = trim($matchesUser[1]);
                } elseif (preg_match('/User: ([\w\s.-]+?)(?: for | assigned| to admin|,|$)/i', $log->description, $matchesUser)) {
                    $parsedTicketUser = trim($matchesUser[1]);
                }
            }
        @endphp
        <div
          class="log-entry {{ str_contains(strtolower($log->description), 'changed') || str_contains($log->action_type, 'notification') || ($log->action_type === 'book' && $log->action === 'Updated book') || $isAssignedTicket ? 'expandable' : '' }}"
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
                
                @if ($isAssignedTicket)
                    <span class="action">accepted ticket</span>
                    @if($parsedTicketId)
                        <span class="affected-item">#{{ $parsedTicketId }}</span>
                    @else
                        {{-- Fallback to original display if ID not found in description or affected_item_id --}}
                        <span class="action" style="margin-left: 4px;">{{ $log->action }}</span>
                        <span class="affected-item">"{{ $log->affected_item_name }}"</span>
                    @endif
                    @if($parsedTicketUser)
                        <span class="description-text" style="margin-left: 4px;">from user {{ $parsedTicketUser }}</span>
                    @endif
                @else
                    <span class="action">{{ $log->action }}</span>
                    @if ($log->affected_item_name && $log->action_type === 'book')
                        @php
                            // Extract book title and author if available
                            $bookInfo = $log->affected_item_name;
                            $description = $log->description;
                            $authorInfo = '';
                            
                            // Try to extract author from description for edited books
                            if ($log->action === 'Updated book') {
                            // First check if author was changed in this update
                            preg_match('/Author changed from \'(.*?)\' to \'(.*?)\'/', $description, $authorMatches);
                            if (!empty($authorMatches)) {
                                $authorInfo = " by " . $authorMatches[2];
                            }
                            }
                            // For deleted books
                            else if ($log->action === 'Deleted book') {
                            // Author might be directly stored in the affected_item_name with format "title by author"
                            if (strpos($bookInfo, ' by ') !== false) {
                                list($title, $author) = explode(' by ', $bookInfo, 2);
                                $bookInfo = $title;
                                $authorInfo = " by " . $author;
                            }
                            }
                            // For uploaded books, we currently don't have the author in the log
                            // We would need to modify the AuditLogService to include the author when uploading
                        @endphp
                        <span class="affected-item">"{{ $bookInfo }}{{ $authorInfo }}"</span>
                    @elseif ($log->affected_item_name)
                        <span class="affected-item">"{{ $log->affected_item_name }}"</span>
                    @endif
                @endif
              </div>
              <span class="timestamp">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            @if (str_contains(strtolower($log->description), 'changed') || str_contains($log->action_type, 'notification') || ($log->action_type === 'book' && $log->action === 'Updated book') || $isAssignedTicket)
              <i class='bx bx-chevron-down accordion-icon' :style="expanded ? 'transform: rotate(180deg)' : ''"></i>
            @endif
          </div>

          @if (str_contains(strtolower($log->description), 'changed') && !$isAssignedTicket)
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
          @elseif($log->action_type === 'book' && $log->action === 'Updated book' && !$isAssignedTicket)
            <div class="log-details" x-cloak x-show="expanded" x-transition style="max-height: none;">
              <div class="changes-list">
                @foreach (explode(', ', $log->description) as $change)
                  <div class="change-item">
                    <i class='bx bx-right-arrow-alt'></i>
                    <span>{{ ucfirst($change) }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          @elseif($log->action_type === 'notification' && !$isAssignedTicket)
            <div class="log-details" x-cloak x-show="expanded" x-transition style="max-height: none;">
              <div class="changes-list">
                <div class="change-item">
                  <i class='bx bx-message-rounded-detail'></i>
                  <span>{{ $log->description }}</span>
                </div>
              </div>
            </div>
          @elseif ($isAssignedTicket)
            <div class="log-details" x-cloak x-show="expanded" x-transition style="max-height: none;">
              <div class="changes-list">
                <div class="change-item">
                  <i class='bx bx-purchase-tag-alt'></i> 
                  @if($parsedTicketId)
                    <span>Ticket ID: #{{ $parsedTicketId }}</span>
                  @else
                    <span>Ticket ID: (Not found in log details)</span>
                  @endif
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

<script>
  // Reset filter dropdowns when filters are cleared
  document.addEventListener('livewire:initialized', function () {
    Livewire.on('filtersCleared', function () {
      document.getElementById('actionTypeFilter').value = 'all';
      document.getElementById('adminIdFilter').value = 'all';
    });
  });
</script>
