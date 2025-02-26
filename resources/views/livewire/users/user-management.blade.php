<div>
    <div class="text-container">
        <h1 class="text-container-title">USER MANAGEMENT</h1>

        <!-- Search and filter controls section -->
        <div class="search-filter-container">
            <div class="search-container">
                <input class="myInput" type="text" wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search for names..." title="Type in a name">
            </div>

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
        <div class="filter-info-row" x-data="{}" x-show="$wire.searchQuery || $wire.filterUserType || $wire.filterBanStatus || $wire.sortOption !== 'newest'">
            <span class="total-count"><span>{{ $totalUsers }}</span> users</span>
            
            <!-- Display active filter tags -->
            <div id="active-filters">
                @if($searchQuery)
                    <span class="filter-tag">Results for '{{ $searchQuery }}'</span>
                @endif
                
                @if($filterUserType)
                    <span class="filter-tag">{{ $filterUserType === 'admin' ? 'Admins' : 'Users' }}</span>
                @endif
                
                @if($filterBanStatus)
                    <span class="filter-tag">{{ $filterBanStatus === 'banned' ? 'Banned' : 'Active' }}</span>
                @endif
                
                @if($sortOption !== 'newest')
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
            
            <button class="clear-filters-btn" wire:click="clearFilters">
                <i class='bx bx-x'></i> Clear Filters
            </button>
        </div>
    </div>

    <!-- Main content section with user table -->
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
                        <th>USER TYPE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through each user in the filtered results -->
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->last_online ? $user->last_online->diffForHumans() : 'Never' }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>{{ ucfirst($user->usertype) }}</td>
                            <td>
                                @if($user->is_banned)
                                    <span class="status-badge banned">Banned</span>
                                @else
                                    <span class="status-badge active">Active</span>
                                @endif
                            </td>
                            <!-- Action buttons - prevent editing your own account -->
                            <td>
                                @if($user->id !== auth()->id())
                                    <a href="{{ route('user.show', $user->id) }}" class="edit-btn">
                                        EDIT
                                    </a>
                                @else
                                    <span class="self-account-badge">CURRENT ACCOUNT</span>
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