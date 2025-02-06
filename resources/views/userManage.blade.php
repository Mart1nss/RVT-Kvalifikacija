<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/usermanage-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">

</head>

<body>


  @include('components.alert')
  @include('navbar')



  <div class="main-container">
    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">User Management
      </h1>
    </div>

    <div class="item-container">
      <div class="filter-div">
        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..."
          title="Type in a name">

        <div class="button-container">
          <div class="dropdown">
            <button id="sortButton" class="sort-dropdown" onclick="toggleDropdown(event, 'sortOptions')">
              <i class='bx bx-sort-alt-2'></i>
              <span id="currentSort">Default</span>
            </button>
            <div id="sortOptions" class="dropdown-content">
              <a href="#" onclick="handleSort('oldest', event)">Default</a>
              <a href="#" onclick="handleSort('nameAZ', event)">Name (A-Z)</a>
              <a href="#" onclick="handleSort('nameZA', event)">Name (Z-A)</a>
              <a href="#" onclick="handleSort('newest', event)">Newest Users</a>
              <a href="#" onclick="handleSort('clear', event)">Oldest Users</a>
              <a href="#" onclick="handleSort('lastOnline', event)">Last Online</a>
              <a href="#" onclick="handleSort('recentlyOnline', event)">Recently Online</a>
            </div>
          </div>

          <div class="dropdown">
            <button id="filterButton" class="sort-dropdown" onclick="toggleDropdown(event, 'filterOptions')">
              <i class='bx bx-filter-alt'></i>
              <span id="currentFilter">All Users</span>
            </button>
            <div id="filterOptions" class="dropdown-content">
              <a href="#" onclick="handleFilter('all', event)">All Users</a>
              <a href="#" onclick="handleFilter('admin', event)">Admins</a>
              <a href="#" onclick="handleFilter('user', event)">Users</a>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <h2>All Users</h2>
        <table id="myTable" class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>User Type</th>
              <th>Last Online</th>
              <th>Created At</th>
              <th>Updated At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
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
                <td>{{ $user->last_online ? $user->last_online->diffForHumans() : 'Never' }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>{{ $user->updated_at->format('M d, Y') }}</td>
                <td>
                  <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;"
                    class="delete-form">
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
        <p>Are you sure you want to delete user "<span id="userName"></span>"?</p>
        <p class="delete-confirmation-text">This action cannot be undone.</p>
      </div>
      <div class="delete-confirmation-footer">
        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="button" class="btn-delete" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>

  <script>
    function myFunction() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("myInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("myTable");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }

    function toggleDropdown(event, dropdownId) {
      event.stopPropagation();
      var dropdowns = document.getElementsByClassName("dropdown-content");
      // Close all other dropdowns
      for (var i = 0; i < dropdowns.length; i++) {
        if (dropdowns[i].id !== dropdownId) {
          dropdowns[i].classList.remove("show");
        }
      }

      var dropdownContent = document.getElementById(dropdownId);
      var currentText = dropdownId === 'sortOptions' ?
        document.getElementById("currentSort").textContent :
        document.getElementById("currentFilter").textContent;

      dropdownContent.classList.toggle("show");

      if (dropdownContent.classList.contains("show")) {
        var options = dropdownContent.getElementsByTagName("a");
        for (var i = 0; i < options.length; i++) {
          options[i].classList.remove("active");
          if (options[i].textContent === currentText) {
            options[i].classList.add("active");
          }
        }
      }
    }

    // Close dropdowns if user clicks anywhere on the page
    document.addEventListener('click', function(event) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var sortButton = document.getElementById("sortButton");
      var filterButton = document.getElementById("filterButton");

      for (var i = 0; i < dropdowns.length; i++) {
        var dropdown = dropdowns[i];
        if (!sortButton.contains(event.target) &&
          !filterButton.contains(event.target) &&
          !dropdown.contains(event.target)) {
          dropdown.classList.remove("show");
        }
      }
    });

    // Prevent clicks inside dropdowns from bubbling to document
    document.querySelectorAll('.dropdown-content').forEach(function(dropdown) {
      dropdown.addEventListener('click', function(event) {
        event.stopPropagation();
      });
    });

    function convertTimeAgoToMinutes(timeAgoText) {
      if (timeAgoText === 'Never') return Number.MAX_SAFE_INTEGER;

      const matches = timeAgoText.match(/(\d+)\s+(second|minute|hour|day|week|month|year)s?\s+ago/);
      if (!matches) return 0;

      const value = parseInt(matches[1]);
      const unit = matches[2];

      switch (unit) {
        case 'second':
          return value / 60;
        case 'minute':
          return value;
        case 'hour':
          return value * 60;
        case 'day':
          return value * 24 * 60;
        case 'week':
          return value * 7 * 24 * 60;
        case 'month':
          return value * 30 * 24 * 60;
        case 'year':
          return value * 365 * 24 * 60;
        default:
          return 0;
      }
    }

    function handleSort(sortType, event) {
      event.preventDefault();
      event.stopPropagation();

      var table = document.getElementById("myTable");
      var rows = Array.from(table.rows).slice(1); // Skip header row
      var tbody = table.getElementsByTagName("tbody")[0];
      var currentSortText = document.getElementById("currentSort");
      var dropdownContent = document.getElementById("sortOptions");

      // Update button text based on selection
      switch (sortType) {
        case 'nameAZ':
          currentSortText.textContent = "Name (A-Z)";
          break;
        case 'nameZA':
          currentSortText.textContent = "Name (Z-A)";
          break;
        case 'newest':
          currentSortText.textContent = "Newest Users";
          break;
        case 'lastOnline':
          currentSortText.textContent = "Last Online";
          break;
        case 'recentlyOnline':
          currentSortText.textContent = "Recently Online";
          break;
        case 'clear':
        case 'oldest':
          currentSortText.textContent = "Default";
          sortType = 'oldest';
          break;
      }

      // Close dropdown after selection
      dropdownContent.classList.remove("show");

      rows.sort(function(a, b) {
        switch (sortType) {
          case 'nameAZ':
            return a.cells[1].textContent.localeCompare(b.cells[1].textContent);
          case 'nameZA':
            return b.cells[1].textContent.localeCompare(a.cells[1].textContent);
          case 'newest':
            return new Date(b.cells[5].textContent) - new Date(a.cells[5].textContent);
          case 'oldest':
            return new Date(a.cells[5].textContent) - new Date(b.cells[5].textContent);
          case 'lastOnline':
            // Convert time ago text to minutes for proper comparison
            const aMinutes = convertTimeAgoToMinutes(a.cells[4].textContent);
            const bMinutes = convertTimeAgoToMinutes(b.cells[4].textContent);
            return bMinutes - aMinutes; // Larger time ago first
          case 'recentlyOnline':
            // Convert time ago text to minutes for proper comparison
            const aMinutesRecent = convertTimeAgoToMinutes(a.cells[4].textContent);
            const bMinutesRecent = convertTimeAgoToMinutes(b.cells[4].textContent);
            return aMinutesRecent - bMinutesRecent; // Smaller time ago first
          default:
            return 0;
        }
      });

      // Clear the table body
      while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
      }

      // Add sorted rows back
      rows.forEach(function(row) {
        tbody.appendChild(row);
      });
    }

    function handleFilter(filterType, event) {
      event.preventDefault();
      event.stopPropagation();

      var table = document.getElementById("myTable");
      var rows = table.getElementsByTagName("tr");
      var currentFilterText = document.getElementById("currentFilter");
      var dropdownContent = document.getElementById("filterOptions");

      // Update button text based on selection
      switch (filterType) {
        case 'admin':
          currentFilterText.textContent = "Admins";
          break;
        case 'user':
          currentFilterText.textContent = "Users";
          break;
        case 'all':
        default:
          currentFilterText.textContent = "All Users";
          break;
      }

      // Close dropdown after selection
      dropdownContent.classList.remove("show");

      // Filter rows
      for (var i = 1; i < rows.length; i++) { // Start from 1 to skip header
        var userTypeCell = rows[i].getElementsByTagName("td")[3]; // User Type column
        if (userTypeCell) {
          var select = userTypeCell.querySelector('select');
          var userType = select ? select.value : ''; // Get the selected value from the select element

          if (filterType === 'all' || userType === filterType) {
            rows[i].style.display = "";
          } else {
            rows[i].style.display = "none";
          }
        }
      }
    }

    // Sort by oldest users by default when page loads
    document.addEventListener('DOMContentLoaded', function() {
      handleSort('oldest', event);
    });

    // Modal functionality
    let currentForm = null;

    function confirmDelete(userId, userName) {
      const modal = document.getElementById('deleteModal');
      const userNameSpan = document.getElementById('userName');
      const confirmBtn = document.getElementById('confirmDeleteBtn');

      currentForm = event.target.closest('form');
      userNameSpan.textContent = userName;
      modal.style.display = 'block';

      confirmBtn.onclick = function() {
        if (currentForm) {
          currentForm.submit();
        }
      }
    }

    function closeModal() {
      const modal = document.getElementById('deleteModal');
      modal.style.display = 'none';
      currentForm = null;
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('deleteModal');
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>


</body>

</html>
