<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Notifications</title>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>

  @include('navbar')
  @include('components.alert')

  <div class="main-container">
    <div class="text-container">
      <h1 class="text-container-title">Send
        Notification</h1>
    </div>

    <div class="item-container">
      <div class="filter-div">


        <form method="POST" action="{{ route('admin.send.notification') }}" class="notification-form"
          id="notificationForm" x-data="{
              recipientType: 'all',
              dropdownOpen: false,
              charCount: 0,
              maxChars: 250,
              message: '',
              adjustHeight(element) {
                  element.style.height = 'auto';
                  element.style.height = (element.scrollHeight) + 'px';
              }
          }">
          @csrf
          <div class="form-group flex-grow">
            <div class="input-wrapper">
              <textarea class="notif-input" name="message" placeholder="Enter your notification message" required maxlength="250"
                x-model="message" @input="adjustHeight($event.target); charCount = message.length" rows="1"></textarea>
              <div class="char-count" :class="{ 'text-danger': charCount >= maxChars }">
                <span x-text="charCount"></span> / <span x-text="maxChars"></span>
              </div>
            </div>
          </div>
          <input type="hidden" name="recipient_type" x-model="recipientType">
          <div class="sort-dropdown" @click.outside="dropdownOpen = false">
            <button type="button" class="btn btn-filter btn-md" @click="dropdownOpen = !dropdownOpen">
              <i class='bx bx-user-circle'></i>
              <span
                x-text="
                recipientType === 'all' ? 'All Users' :
                recipientType === 'users' ? 'Users Only' :
                recipientType === 'admins' ? 'Admins Only' :
                'Yourself'
              "></span>
            </button>
            <ul class="dropdown-content" :class="{ 'show': dropdownOpen }">
              <li @click="recipientType = 'all'; dropdownOpen = false"
                :class="{ 'selected': recipientType === 'all' }">All Users</li>
              <li @click="recipientType = 'users'; dropdownOpen = false"
                :class="{ 'selected': recipientType === 'users' }">Users Only</li>
              <li @click="recipientType = 'admins'; dropdownOpen = false"
                :class="{ 'selected': recipientType === 'admins' }">Admins Only</li>
              <li @click="recipientType = 'self'; dropdownOpen = false"
                :class="{ 'selected': recipientType === 'self' }">Yourself (Testing)</li>
            </ul>
          </div>
          <button class="btn btn-primary btn-md" type="submit">Send</button>
        </form>
      </div>
    </div>

    <div class="notifications-list">
      <div class="notifications-header-page">
        <h2>Sent Notifications</h2>
        <div class="filter-buttons">
          <div class="sort-dropdown">
            <button type="button" class="btn btn-filter btn-md" id="sortButton">
              <i class='bx bx-sort-alt-2'></i>
              <span id="sortTypeText">Newest</span>
            </button>
            <ul class="dropdown-content" id="sortDropdown">
              <li data-sort="newest" class="selected">Newest</li>
              <li data-sort="oldest">Oldest</li>
            </ul>
          </div>

          <div class="genre-dropdown">
            <button type="button" class="btn btn-filter btn-md" id="filterButton">
              <i class='bx bx-filter-alt'></i>
              <span>Filter Types</span>
            </button>
            <ul class="dropdown-content" id="filterDropdown">
              <li>
                <label class="genre-checkbox-container">
                  <input type="checkbox" value="all" class="type-checkbox">
                  <span class="custom-checkbox"></span>
                  <span class="genre-name">All Users</span>
                </label>
              </li>
              <li>
                <label class="genre-checkbox-container">
                  <input type="checkbox" value="users" class="type-checkbox">
                  <span class="custom-checkbox"></span>
                  <span class="genre-name">Users Only</span>
                </label>
              </li>
              <li>
                <label class="genre-checkbox-container">
                  <input type="checkbox" value="admins" class="type-checkbox">
                  <span class="custom-checkbox"></span>
                  <span class="genre-name">Admins Only</span>
                </label>
              </li>
              <li>
                <label class="genre-checkbox-container">
                  <input type="checkbox" value="self" class="type-checkbox">
                  <span class="custom-checkbox"></span>
                  <span class="genre-name">Yourself</span>
                </label>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="filter-info-row" id="filterInfoRow" style="display: none;">
        <div class="filter-info-left">
          <span class="total-count">
            <span id="filteredCount">0</span> notifications
          </span>
          <div id="activeFilters" style="display: none;">
          </div>
        </div>
        <button class="clear-filters-btn" id="clearFiltersBtn" style="display: none;">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      </div>

      <div id="notificationsContainer">
        <div class="notifications-cards">
          @include('partials.notification-cards')
        </div>
        <div class="pagination-container">
          {{ $notifications->links('vendor.pagination.tailwind') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="delete-modal" id="deleteModal">
    <div class="delete-modal-content">
      <div class="delete-modal-header">
        <h3>Delete Notification</h3>
      </div>
      <div class="delete-modal-body">
        <p>Are you sure you want to delete this notification? This will remove it from all users' notifications.</p>
      </div>
      <div class="delete-modal-footer">
        <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
        <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
      </div>
    </div>
  </div>

  <script>
    let currentNotificationId = null;

    function openDeleteModal(id) {
      currentNotificationId = id;
      const modal = document.getElementById('deleteModal');
      modal.style.display = 'flex';
    }

    function closeDeleteModal() {
      const modal = document.getElementById('deleteModal');
      modal.style.display = 'none';
      currentNotificationId = null;
    }

    function confirmDelete() {
      if (!currentNotificationId) return;

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/notifications/sent/${currentNotificationId}/delete`;

      // Add CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);

      document.body.appendChild(form);
      form.submit();
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
      const modal = document.getElementById('deleteModal');
      if (event.target === modal) {
        closeDeleteModal();
      }
    });

    document.getElementById('notificationForm').addEventListener('submit', function(e) {
      // Regular form submission, no need to prevent default
      const submitButton = this.querySelector('button[type="submit"]');
      submitButton.disabled = true;
      submitButton.innerHTML = 'Sending...';
    });

    // State management
    const state = {
      sortType: 'newest',
      selectedTypes: [],
      baseUrl: '{{ url('/notifications') }}',
      totalCount: {{ $totalCount }},
      filteredCount: {{ $filteredCount }},
      recipientTypes: {
        all: 'All Users',
        users: 'Users Only',
        admins: 'Admins Only',
        self: 'Yourself'
      }
    };

    // DOM Elements
    const elements = {
      sortButton: document.getElementById('sortButton'),
      sortDropdown: document.getElementById('sortDropdown'),
      filterButton: document.getElementById('filterButton'),
      filterDropdown: document.getElementById('filterDropdown'),
      sortTypeText: document.getElementById('sortTypeText'),
      filterInfoRow: document.getElementById('filterInfoRow'),
      activeFilters: document.getElementById('activeFilters'),
      clearFiltersBtn: document.getElementById('clearFiltersBtn'),
      filteredCount: document.getElementById('filteredCount'),
      notificationsContainer: document.getElementById('notificationsContainer')
    };

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
      initializeFilters();
      setupEventListeners();
    });

    // Initialize filters from URL parameters
    function initializeFilters() {
      try {
        const urlParams = new URLSearchParams(window.location.search);
        state.sortType = urlParams.get('sort') || 'newest';
        state.selectedTypes = (urlParams.get('types') || '').split(',').filter(Boolean);

        // Update UI to reflect current filters
        elements.sortTypeText.textContent = state.sortType === 'newest' ? 'Newest' : 'Oldest';

        // Update checkboxes
        document.querySelectorAll('.type-checkbox').forEach(checkbox => {
          checkbox.checked = state.selectedTypes.includes(checkbox.value);
        });

        updateUI();
      } catch (error) {
        console.error('Error initializing filters:', error);
      }
    }

    // Setup all event listeners
    function setupEventListeners() {
      // Sort dropdown
      elements.sortButton.addEventListener('click', () => toggleDropdown('sort'));
      elements.sortDropdown.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', () => handleSortChange(item.dataset.sort));
      });

      // Filter checkboxes
      elements.filterButton.addEventListener('click', () => toggleDropdown('filter'));
      document.querySelectorAll('.type-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', () => handleFilterChange(checkbox));
      });

      // Clear filters button
      elements.clearFiltersBtn.addEventListener('click', clearAllFilters);

      // Pagination clicks
      document.addEventListener('click', handlePaginationClick);

      // Close dropdowns when clicking outside
      document.addEventListener('click', handleClickOutside);
    }

    // Toggle dropdown visibility
    function toggleDropdown(type) {
      const dropdown = type === 'sort' ? elements.sortDropdown : elements.filterDropdown;
      const otherDropdown = type === 'sort' ? elements.filterDropdown : elements.sortDropdown;

      dropdown.classList.toggle('show');
      otherDropdown.classList.remove('show');
      event.stopPropagation();
    }

    // Handle sort type change
    function handleSortChange(newSortType) {
      state.sortType = newSortType;
      elements.sortTypeText.textContent = newSortType === 'newest' ? 'Newest' : 'Oldest';
      elements.sortDropdown.classList.remove('show');
      updateURL();
    }

    // Handle filter changes
    function handleFilterChange(checkbox) {
      const value = checkbox.value;
      const index = state.selectedTypes.indexOf(value);

      if (checkbox.checked && index === -1) {
        state.selectedTypes.push(value);
      } else if (!checkbox.checked && index > -1) {
        state.selectedTypes.splice(index, 1);
      }

      updateURL();
    }

    // Clear all filters
    function clearAllFilters() {
      state.selectedTypes = [];
      state.sortType = 'newest';
      document.querySelectorAll('.type-checkbox').forEach(cb => cb.checked = false);
      elements.sortTypeText.textContent = 'Newest';
      updateURL();
    }

    // Handle pagination clicks
    function handlePaginationClick(e) {
      const link = e.target.closest('.pagination a');
      if (link) {
        e.preventDefault();
        loadPage(link.href);
      }
    }

    // Close dropdowns when clicking outside
    function handleClickOutside(e) {
      if (!e.target.closest('.sort-dropdown') && !e.target.closest('.genre-dropdown')) {
        elements.sortDropdown.classList.remove('show');
        elements.filterDropdown.classList.remove('show');
      }
    }

    // Load page with new data
    async function loadPage(url) {
      try {
        // Show loading state
        const notificationsContainer = document.getElementById('notificationsContainer');
        if (notificationsContainer) {
          notificationsContainer.style.opacity = '0.5';
        }

        // Convert relative URLs to absolute URLs
        const absoluteUrl = new URL(url, window.location.origin);

        const response = await fetch(absoluteUrl, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });

        if (!response.ok) {
          const data = await response.json();
          throw new Error(data.error || `HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Update the notifications container
        if (notificationsContainer) {
          // Update the cards section
          const cardsContainer = notificationsContainer.querySelector('.notifications-cards');
          if (cardsContainer) {
            cardsContainer.innerHTML = data.html;
          }

          // Update the pagination section if it exists
          const paginationContainer = notificationsContainer.querySelector('.pagination-container');
          if (paginationContainer) {
            paginationContainer.innerHTML = data.pagination || '';
          }

          notificationsContainer.style.opacity = '1';
        }

        // Update state
        state.totalCount = data.total || 0;
        state.filteredCount = data.filteredCount || 0;

        // Update UI
        updateUI();

        // Update URL without reload
        window.history.pushState({}, '', absoluteUrl);

        // Update pagination links with current filters if pagination exists
        if (data.pagination) {
          updatePaginationLinks();
        }
      } catch (error) {
        console.error('Error loading page:', error);
        // Show error message to user
        const notificationsContainer = document.getElementById('notificationsContainer');
        if (notificationsContainer) {
          notificationsContainer.style.opacity = '1';
          notificationsContainer.innerHTML = `
            <div class="error-message" style="text-align: center; padding: 20px; color: #ff6b6b;">
              <p>Failed to load notifications. Please try refreshing the page.</p>
              <p>Error: ${error.message}</p>
            </div>
          `;
        }
      }
    }

    // Update pagination links with current filters
    function updatePaginationLinks() {
      const paginationLinks = document.querySelectorAll('.pagination a');
      paginationLinks.forEach(link => {
        try {
          const url = new URL(link.href, window.location.origin);

          // Remove any existing filter parameters
          url.searchParams.delete('types');
          url.searchParams.delete('sort');

          // Add current filters
          if (state.selectedTypes.length > 0) {
            url.searchParams.set('types', state.selectedTypes.join(','));
          }
          if (state.sortType !== 'newest') {
            url.searchParams.set('sort', state.sortType);
          }

          link.href = url.toString();
        } catch (error) {
          console.error('Error updating pagination link:', error);
        }
      });
    }

    // Update URL and load new data
    function updateURL() {
      try {
        const url = new URL(window.location.href);

        // Reset to first page when filtering
        url.searchParams.delete('page');

        // Update filter parameters
        if (state.selectedTypes.length > 0) {
          url.searchParams.set('types', state.selectedTypes.join(','));
        } else {
          url.searchParams.delete('types');
        }

        if (state.sortType !== 'newest') {
          url.searchParams.set('sort', state.sortType);
        } else {
          url.searchParams.delete('sort');
        }

        loadPage(url.toString());
      } catch (error) {
        console.error('Error updating URL:', error);
      }
    }

    // Update UI elements based on current state
    function updateUI() {
      elements.filteredCount.textContent = state.filteredCount;
      elements.filterInfoRow.style.display = state.totalCount > 0 ? 'flex' : 'none';

      // Update active filters
      const hasFilters = state.selectedTypes.length > 0 || state.sortType !== 'newest';
      elements.clearFiltersBtn.style.display = hasFilters ? 'flex' : 'none';

      // Update active filters display
      if (hasFilters) {
        const filterTags = [];
        state.selectedTypes.forEach(type => {
          filterTags.push(`<span class="filter-tag">${state.recipientTypes[type]}</span>`);
        });
        elements.activeFilters.innerHTML = filterTags.join('');
        elements.activeFilters.style.display = filterTags.length ? 'flex' : 'none';
      } else {
        elements.activeFilters.style.display = 'none';
      }
    }
  </script>

</body>

</html>
