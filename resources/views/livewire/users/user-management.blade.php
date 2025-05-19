<div>
  <div class="text-container" style="background: transparent; padding: 0px; margin-bottom: 20px;">
    <h1 class="text-container-title">USER MANAGEMENT</h1>
  </div>

  <!-- Mobile Filter Drawer Component -->
  <x-filters.mobile-filter-drawer title="Filter Options">
    <!-- Sort Options -->
    <x-filters.filter-item label="Sort By">
      <select wire:model.live="sortOption" class="drawer-select">
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
        <option value="nameAZ">Name (A-Z)</option>
        <option value="nameZA">Name (Z-A)</option>
        <option value="lastOnline">Last Online</option>
      </select>
    </x-filters.filter-item>

    <!-- User Type Filter -->
    <x-filters.filter-item label="User Type">
      <select wire:model.live="filterUserType" class="drawer-select">
        <option value="">All Users</option>
        <option value="admin">Admins</option>
        <option value="user">Users</option>
      </select>
    </x-filters.filter-item>

    <!-- User Status Filter -->
    <x-filters.filter-item label="User Status">
      <select wire:model.live="filterBanStatus" class="drawer-select">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="banned">Banned</option>
      </select>
    </x-filters.filter-item>

    <x-slot name="footer">
      <button class="drawer-clear-btn" wire:click="clearFilters">
        <i class='bx bx-trash'></i> Clear All Filters
      </button>
    </x-slot>
  </x-filters.mobile-filter-drawer>





  <!-- Main content section with user table -->
  <div class="item-container"  style="border-radius: 8px;">
  <h2>All Users</h2>

<!-- Search and filter controls section -->
<div class="search-filter-container">
  <div class="search-container">
    <input class="myInput" type="text" wire:model.live.debounce.300ms="searchQuery"
      placeholder="Search by name or email..." title="Type in a name">
  </div>

  <!-- Mobile filter button - only visible on small screens -->
  <button class="mobile-filter-btn" id="mobileFilterBtn">
    <i class='bx bx-filter-alt'></i>
  </button>

  <!-- Sort dropdown - changes are applied immediately with wire:model.live -->
  <div class="sort-dropdown">
    <select wire:model.live="sortOption" class="btn btn-filter btn-md">
      <option value="newest">Newest</option>
      <option value="oldest">Oldest</option>
      <option value="nameAZ">Name (A-Z)</option>
      <option value="nameZA">Name (Z-A)</option>
      <option value="lastOnline">Last Online</option>
    </select>
  </div>

  <!-- User type filter dropdown (admin/user) -->
  <div class="sort-dropdown">
    <select wire:model.live="filterUserType" class="btn btn-filter btn-md">
      <option value="">All Users</option>
      <option value="admin">Admins</option>
      <option value="user">Users</option>
    </select>
  </div>

  <!-- Ban status filter dropdown (active/banned) -->
  <div class="sort-dropdown">
    <select wire:model.live="filterBanStatus" class="btn btn-filter btn-md">
      <option value="">All Statuses</option>
      <option value="active">Active</option>
      <option value="banned">Banned</option>
    </select>
  </div>
</div>

<!-- Active filters display - only shown when filters are applied -->
@if ($this->showFilterInfo())
  <div class="filter-info-row" style="margin-bottom: 10px;">
    <span class="total-count" style="color: #999;"><span>{{ $totalUsers }}</span> users</span>

    <!-- Display active filter tags -->
    <div id="active-filters">
      @if ($searchQuery)
        <span class="filter-tag">Results for '{{ $searchQuery }}'</span>
      @endif

      @if ($filterUserType)
        <span class="filter-tag">{{ $filterUserType === 'admin' ? 'Admins' : 'Users' }}</span>
      @endif

      @if ($filterBanStatus)
        <span class="filter-tag">{{ $filterBanStatus === 'banned' ? 'Banned' : 'Active' }}</span>
      @endif

      @if ($sortOption !== 'newest')
        <span class="filter-tag">
          @switch($sortOption)
            @case('oldest')
              Oldest
            @break

            @case('nameAZ')
              Name (A-Z)
            @break

            @case('nameZA')
              Name (Z-A)
            @break

            @case('lastOnline')
              Last Online
            @break
          @endswitch
        </span>
      @endif
    </div>

    @if ($this->hasActiveFilters())
      <button class="clear-filters-btn" wire:click="clearFilters" id="clearFiltersBtn">
        <i class='bx bx-x'></i> Clear Filters
      </button>
    @endif
  </div>
@endif
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th class="hide-small">ID</th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th class="hide-small">LAST ONLINE</th>
            <th class="hide-small">CREATED AT</th>
            <th>USER TYPE</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through each user in the filtered results -->
          @foreach ($users as $user)
            <tr>
              <td class="hide-small">{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td class="hide-small">{{ $user->last_online ? $user->last_online->diffForHumans() : 'Never' }}
              </td>
              <td class="hide-small">{{ $user->created_at->format('M d, Y') }}</td>
              <td>{{ ucfirst($user->usertype) }}</td>
              <td>
                @if ($user->isBanned())
                  <span class="status-badge banned">Banned</span>
                @else
                  <span class="status-badge active">Active</span>
                @endif
              </td>
              <!-- Action buttons - prevent editing your own account -->
              <td>
                @if ($user->id !== auth()->id())
                  <a href="{{ route('user.show', $user->id) }}" class="edit-btn">
                    EDIT
                  </a>
                @else
                  <span class="self-account-badge">YOU</span>
                @endif
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
