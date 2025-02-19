<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/usermanage-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
  <!-- Add Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>


  @include('components.alert')
  @include('navbar')


  <div class="main-container" x-data="userManagement">
    <div class="text-container">
      <h1 class="text-container-title">USER MANAGEMENT</h1>



      <div class="search-filter-container">
        <div class="search-container">
          <input class="myInput" type="text" x-model="searchQuery" @input.debounce.300ms="updateResults"
            placeholder="Search for names..." title="Type in a name">
        </div>

        <div class="sort-dropdown">
          <button class="btn btn-filter btn-md" @click.stop="toggleDropdown('sort')">
            <i class='bx bx-sort-alt-2'></i>
            <span x-text="getSortDisplayText()"></span>
          </button>
          <ul class="dropdown-content" :class="{ 'show': dropdowns.sort }">
            <template x-for="option in sortOptions" :key="option.value">
              <li :class="{ 'selected': currentSort === option.value }" @click="updateSort(option.value)"
                x-text="option.text">
              </li>
            </template>
          </ul>
        </div>

        <div class="sort-dropdown">
          <button class="btn btn-filter btn-md" @click.stop="toggleDropdown('filter')">
            <i class='bx bx-filter-alt'></i>
            <span x-text="getFilterDisplayText()"></span>
          </button>
          <ul class="dropdown-content" :class="{ 'show': dropdowns.filter }">
            <template x-for="option in filterOptions" :key="option.value">
              <li :class="{ 'selected': currentFilter === option.value }" @click="updateFilter(option.value)"
                x-text="option.text">
              </li>
            </template>
          </ul>
        </div>
      </div>

      <div class="filter-info-row" x-show="showFilterInfo">
        <span class="total-count"><span x-text="totalUsers"></span> users</span>
        <div id="active-filters">
          <template x-if="searchQuery">
            <span class="filter-tag" x-text="`Results for '${searchQuery}'`"></span>
          </template>
          <template x-if="currentFilter !== 'all'">
            <span class="filter-tag" x-text="getFilterDisplayText()"></span>
          </template>
          <template x-if="currentSort !== 'newest'">
            <span class="filter-tag" x-text="getSortDisplayText()"></span>
          </template>
        </div>
        <button class="clear-filters-btn" @click="clearAllFilters" x-show="hasActiveFilters">
          <i class='bx bx-x'></i> Clear Filters
        </button>
      </div>
    </div>


    <div class="item-container">
      <div class="table-responsive">
        <h2>All Users</h2>
        <table class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>NAME</th>
              <th>EMAIL</th>
              <th>LAST ONLINE</th>
              <th>CREATED AT</th>
              <th>UPDATED AT</th>
              <th>USER TYPE</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->last_online ? $user->last_online->diffForHumans() : 'Never' }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>{{ $user->updated_at->format('M d, Y') }}</td>
                <td>
                  <form action="{{ route('users.updateUserType', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <select name="usertype" onchange="this.form.submit()" class="usertype-select">
                      <option value="user" {{ $user->usertype == 'user' ? 'selected' : '' }}>User</option>
                      <option value="admin" {{ $user->usertype == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                  </form>
                </td>
                <td>
                  <form action="{{ route('users.destroy', $user) }}" method="POST" class="delete-form"
                    data-user-id="{{ $user->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="remove-btn"
                      onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                      DELETE
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="pagination-container">
          {{ $users->links('vendor.pagination.tailwind') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="delete-confirmation-modal">
    <div class="delete-confirmation-content">
      <div class="delete-confirmation-header">
        <h2>Delete User</h2>
      </div>
      <div class="delete-confirmation-body">
        <p>Are you sure you want to delete user "<span id="deleteUserName"></span>"?</p>
        <p class="delete-confirmation-text">This action cannot be undone.</p>
      </div>
      <div class="delete-confirmation-footer">
        <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
        <button type="button" class="btn-delete" id="confirmDeleteBtn" onclick="submitDelete()">Delete</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('userManagement', () => ({
        searchQuery: '',
        currentSort: 'newest',
        currentFilter: 'all',
        showModal: false,
        totalUsers: {{ $users->total() }},
        userToDelete: {
          id: null,
          name: '',
          form: null
        },
        dropdowns: {
          sort: false,
          filter: false
        },
        sortOptions: [{
            value: 'newest',
            text: 'Newest'
          },
          {
            value: 'oldest',
            text: 'Oldest'
          },
          {
            value: 'nameAZ',
            text: 'Name (A-Z)'
          },
          {
            value: 'nameZA',
            text: 'Name (Z-A)'
          },
          {
            value: 'lastOnline',
            text: 'Last Online'
          }
        ],
        filterOptions: [{
            value: 'all',
            text: 'All Users'
          },
          {
            value: 'admin',
            text: 'Admins'
          },
          {
            value: 'user',
            text: 'Users'
          }
        ],

        init() {
          this.initFromUrl();

          // Close dropdowns when clicking outside
          document.addEventListener('click', () => {
            this.dropdowns.sort = false;
            this.dropdowns.filter = false;
          });

          // Handle pagination clicks
          document.addEventListener('click', (e) => {
            const link = e.target.closest('.pagination a');
            if (link) {
              e.preventDefault();
              this.loadPage(link.href);
            }
          });
        },

        get showFilterInfo() {
          return this.searchQuery ||
            this.currentFilter !== 'all' ||
            this.currentSort !== 'newest';
        },

        get hasActiveFilters() {
          return this.showFilterInfo;
        },

        initFromUrl() {
          const urlParams = new URLSearchParams(window.location.search);
          this.searchQuery = urlParams.get('query') || '';
          this.currentSort = urlParams.get('sort') || 'newest';
          this.currentFilter = urlParams.get('filter') || 'all';
        },

        toggleDropdown(type) {
          Object.keys(this.dropdowns).forEach(key => {
            if (key !== type) this.dropdowns[key] = false;
          });
          this.dropdowns[type] = !this.dropdowns[type];
        },

        getSortDisplayText() {
          const option = this.sortOptions.find(opt => opt.value === this.currentSort);
          return option ? option.text : 'Newest';
        },

        getFilterDisplayText() {
          const option = this.filterOptions.find(opt => opt.value === this.currentFilter);
          return option ? option.text : 'All Users';
        },

        updateSort(value) {
          this.currentSort = value;
          this.dropdowns.sort = false;
          this.updateResults();
        },

        updateFilter(value) {
          this.currentFilter = value;
          this.dropdowns.filter = false;
          this.updateResults();
        },

        async loadPage(url) {
          try {
            const response = await fetch(url, {
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();

            // Update the table content
            document.querySelector('.table-responsive').innerHTML = data.html;

            // Update URL without reloading
            const newUrl = new URL(url);

            // Preserve the current filters in the URL
            if (this.searchQuery && !newUrl.searchParams.has('query')) {
              newUrl.searchParams.set('query', this.searchQuery);
            }
            if (this.currentSort !== 'newest' && !newUrl.searchParams.has('sort')) {
              newUrl.searchParams.set('sort', this.currentSort);
            }
            if (this.currentFilter !== 'all' && !newUrl.searchParams.has('filter')) {
              newUrl.searchParams.set('filter', this.currentFilter);
            }

            window.history.pushState({}, '', newUrl);

            // Update total users count
            this.totalUsers = data.total;

            // Update the pagination links to include current filters
            const paginationLinks = document.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
              const linkUrl = new URL(link.href);
              if (this.searchQuery) {
                linkUrl.searchParams.set('query', this.searchQuery);
              }
              if (this.currentSort !== 'newest') {
                linkUrl.searchParams.set('sort', this.currentSort);
              }
              if (this.currentFilter !== 'all') {
                linkUrl.searchParams.set('filter', this.currentFilter);
              }
              link.href = linkUrl.toString();
            });
          } catch (error) {
            console.error('Error:', error);
          }
        },

        async updateResults() {
          const url = new URL(window.location.href);

          // Clear existing parameters
          url.searchParams.delete('page'); // Reset to first page when filtering

          if (this.searchQuery) {
            url.searchParams.set('query', this.searchQuery);
          } else {
            url.searchParams.delete('query');
          }

          if (this.currentSort !== 'newest') {
            url.searchParams.set('sort', this.currentSort);
          } else {
            url.searchParams.delete('sort');
          }

          if (this.currentFilter !== 'all') {
            url.searchParams.set('filter', this.currentFilter);
          } else {
            url.searchParams.delete('filter');
          }

          await this.loadPage(url.toString());
        },

        clearAllFilters() {
          this.searchQuery = '';
          this.currentSort = 'newest';
          this.currentFilter = 'all';
          this.updateResults();
        },

        confirmDelete(userId, userName) {
          this.showModal = true;
          this.userToDelete = {
            id: userId,
            name: userName,
            form: event.target.closest('form')
          };
        },

        closeModal() {
          this.showModal = false;
          this.userToDelete = {
            id: null,
            name: '',
            form: null
          };
        },

        submitDelete() {
          if (this.userToDelete.form) {
            this.userToDelete.form.submit();
          }
          this.closeModal();
        }
      }));
    });
  </script>


  <script>
    let deleteForm = null;

    function confirmDelete(userId, userName) {
      // Get the form directly without using route helper
      deleteForm = document.querySelector(`form.delete-form[data-user-id="${userId}"]`);
      document.getElementById('deleteUserName').textContent = userName;
      document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').style.display = 'none';
      deleteForm = null;
    }

    function submitDelete() {
      if (deleteForm) {
        // Submit form using fetch to handle the response
        fetch(deleteForm.action, {
            method: 'POST',
            body: new FormData(deleteForm),
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Show success alert using the existing alert component
              const alertContainer = document.querySelector('.alert-container');
              if (alertContainer) {
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success';
                successAlert.innerHTML = `
                            <div class="alert-content">
                                <i class='bx bx-check-circle'></i>
                                <span>User successfully deleted!</span>
                            </div>
                        `;
                alertContainer.appendChild(successAlert);

                // Remove alert after 3 seconds
                setTimeout(() => {
                  successAlert.remove();
                }, 3000);
              }

              // Refresh the page to show updated user list
              window.location.reload();
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      }
      closeDeleteModal();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('deleteModal');
      if (event.target === modal) {
        closeDeleteModal();
      }
    }
  </script>


</body>

</html>
