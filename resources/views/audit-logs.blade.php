<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('css/audit-logs.css') }}">
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <title>Audit Logs</title>

</head>


<body>
  @include('navbar')

  <div class="main-container audit-logs">
    <div class="text-container">
      <h1 class="h1-text">Audit Logs</h1>
      <p class="retention-notice">Logs are automatically deleted after one week</p>
    </div>

    <div class="item-container">
      <div class="filter-container">
        <div class="button-container">
          <div class="dropdown">
            <button id="actionButton" class="sort-dropdown" onclick="toggleDropdown(event, 'actionOptions')">
              <i class='bx bx-filter-alt'></i>
              <span id="currentAction">All Actions</span>
            </button>
            <div id="actionOptions" class="dropdown-content">
              <a href="#" onclick="handleFilter('all', 'action', event)">
                <i class='bx bx-check-circle'></i> All Actions
              </a>
              <a href="#" onclick="handleFilter('book', 'action', event)">
                <i class='bx bx-book-alt'></i> Book
              </a>
              <a href="#" onclick="handleFilter('user', 'action', event)">
                <i class='bx bx-user'></i> User
              </a>
              <a href="#" onclick="handleFilter('category', 'action', event)">
                <i class='bx bx-category'></i> Category
              </a>
              <a href="#" onclick="handleFilter('notification', 'action', event)">
                <i class='bx bx-bell'></i> Notification
              </a>
            </div>
          </div>

          <div class="dropdown">
            <button id="adminButton" class="sort-dropdown" onclick="toggleDropdown(event, 'adminOptions')">
              <i class='bx bx-user'></i>
              <span id="currentAdmin">All Admins</span>
            </button>
            <div id="adminOptions" class="dropdown-content">
              <a href="#" onclick="handleFilter('all', 'admin', event)">All Admins</a>
              @foreach ($admins as $admin)
                <a href="#" onclick="handleFilter('{{ $admin->id }}', 'admin', event)">{{ $admin->name }}</a>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <div class="logs-container">
        @foreach ($logs as $log)
          <div
            class="log-entry {{ str_contains($log->description, 'Changed') || str_contains($log->action_type, 'notification') ? 'expandable' : '' }}"
            data-action="{{ $log->action_type }}" data-admin="{{ $log->admin_id }}"
            onclick="{{ str_contains($log->description, 'Changed') || str_contains($log->action_type, 'notification') ? 'toggleAccordion(this)' : '' }}">
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
                    @endif
                  </span>
                  <span class="admin-name">{{ $log->admin->name }}</span>
                  <span class="action">{{ $log->action }}</span>
                  @if ($log->affected_item_name)
                    <span class="affected-item">"{{ $log->affected_item_name }}"</span>
                  @endif
                </div>
                <span class="timestamp">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
              </div>
              @if (str_contains($log->description, 'Changed') || str_contains($log->action_type, 'notification'))
                <i class='bx bx-chevron-down accordion-icon'></i>
              @endif
            </div>

            @if (str_contains($log->description, 'Changed'))
              <div class="log-details">
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
              <div class="log-details">
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
          {{ $logs->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleDropdown(event, dropdownId) {
      event.stopPropagation();

      const dropdowns = document.getElementsByClassName("dropdown-content");
      const clickedDropdown = document.getElementById(dropdownId);

      // Close all other dropdowns
      for (let dropdown of dropdowns) {
        if (dropdown.id !== dropdownId && dropdown.classList.contains('show')) {
          dropdown.classList.remove('show');
        }
      }

      // Toggle clicked dropdown
      clickedDropdown.classList.toggle('show');
    }

    function handleFilter(value, type, event) {
      event.preventDefault();
      event.stopPropagation();

      const currentActionText = document.getElementById("currentAction");
      const currentAdminText = document.getElementById("currentAdmin");
      const dropdownContent = event.target.closest('.dropdown-content');
      const logs = document.querySelectorAll('.log-entry');

      // Remove active class from all options in the dropdown
      dropdownContent.querySelectorAll('a').forEach(a => a.classList.remove('active'));
      // Add active class to clicked option
      event.target.closest('a').classList.add('active');

      if (type === 'action') {
        currentActionText.textContent = event.target.textContent.trim();
      } else {
        currentAdminText.textContent = event.target.textContent.trim();
      }

      dropdownContent.classList.remove("show");

      logs.forEach(log => {
        const actionMatch = value === 'all' || log.dataset.action === value;
        const adminMatch = document.getElementById("currentAdmin").textContent === 'All Admins' ||
          log.dataset.admin === value;

        if (type === 'action') {
          log.style.display = actionMatch &&
            (document.getElementById("currentAdmin").textContent === 'All Admins' ||
              log.dataset.admin === document.getElementById("currentAdmin").textContent) ? "" : "none";
        } else {
          log.style.display = adminMatch &&
            (document.getElementById("currentAction").textContent === 'All Actions' ||
              log.dataset.action === document.getElementById("currentAction").textContent.toLowerCase()) ? "" :
            "none";
        }
      });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
      if (!event.target.closest('.dropdown')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let dropdown of dropdowns) {
          dropdown.classList.remove('show');
        }
      }
    });

    function toggleAccordion(element) {
      if (!element.classList.contains('expandable')) return;

      const details = element.querySelector('.log-details');
      const icon = element.querySelector('.accordion-icon');

      if (details) {
        const isExpanded = details.classList.contains('expanded');

        // Toggle the expanded class
        details.classList.toggle('expanded');

        // Rotate the icon
        icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';

        // Animate the height
        if (isExpanded) {
          details.style.maxHeight = '0';
        } else {
          details.style.maxHeight = details.scrollHeight + 'px';
        }
      }
    }
  </script>
</body>

</html>
