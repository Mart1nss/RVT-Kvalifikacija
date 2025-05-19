<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * UserManagement Component
 * 
 * This Livewire component handles the user management functionality including:
 * - Displaying a list of users with pagination
 * - Searching users by name or email
 * - Filtering users by type (admin/user) and status (active/banned)
 * - Sorting users by different criteria
 */
class UserManagement extends Component
{
    use WithPagination;

    // Public properties that can be bound to the UI
    public $searchQuery = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filterUserType = '';
    public $filterBanStatus = '';
    public $sortOption = 'newest';

    // Define which properties should be included in the URL query string
    // This allows filters to persist when sharing URLs or refreshing the page
    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'sortOption' => ['except' => 'newest'],
        'filterUserType' => ['except' => ''],
        'filterBanStatus' => ['except' => ''],
    ];

    /**
     * Initialize the component
     * This method is called when the component is first loaded
     */
    public function mount()
    {
        $this->updateSortFieldAndDirection();
    }

    /**
     * Render the component view
     * This method is called whenever the component needs to be re-rendered
     */
    public function render()
    {
        $users = $this->getUsers();
        return view('livewire.users.user-management', [
            'users' => $users,
            'totalUsers' => $this->getTotalUsers(),
        ]);
    }

    /**
     * Get filtered and sorted users with pagination
     * This method applies all active filters and sorting to the user query
     */
    public function getUsers()
    {
        $query = User::query();

        // Apply search filter - looks for matching name or email
        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('email', 'like', "%{$this->searchQuery}%");
            });
        }

        // Apply user type filter - admin or regular user
        if (!empty($this->filterUserType) && in_array($this->filterUserType, ['admin', 'user'])) {
            $query->where('usertype', $this->filterUserType);
        }

        // Apply ban status filter - active or banned users
        if ($this->filterBanStatus === 'banned') {
            $query->whereHas('activeBan');
        } elseif ($this->filterBanStatus === 'active') {
            $query->whereDoesntHave('activeBan');
        }

        // Apply sorting based on selected field and direction
        $query->orderBy($this->sortField, $this->sortDirection);

        // Return paginated results (10 users per page)
        return $query->paginate(10);
    }

    /**
     * Get total count of filtered users
     * This method applies the same filters as getUsers() but returns a count instead
     */
    public function getTotalUsers()
    {
        $query = User::query();

        // Apply search filter
        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('email', 'like', "%{$this->searchQuery}%");
            });
        }

        // Apply user type filter
        if (!empty($this->filterUserType) && in_array($this->filterUserType, ['admin', 'user'])) {
            $query->where('usertype', $this->filterUserType);
        }

        // Apply ban status filter
        if ($this->filterBanStatus === 'banned') {
            $query->whereHas('activeBan');
        } elseif ($this->filterBanStatus === 'active') {
            $query->whereDoesntHave('activeBan');
        }

        // Return the total count
        return $query->count();
    }

    /**
     * Reset pagination when search query changes
     * This ensures we start from page 1 when applying a new search
     */
    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when user type filter changes
     */
    public function updatedFilterUserType()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when ban status filter changes
     */
    public function updatedFilterBanStatus()
    {
        $this->resetPage();
    }

    /**
     * Update sort settings and reset pagination when sort option changes
     */
    public function updatedSortOption()
    {
        $this->updateSortFieldAndDirection();
        $this->resetPage();
    }

    /**
     * Convert user-friendly sort option to actual field and direction
     * This method translates options like "newest" to the appropriate database fields
     */
    private function updateSortFieldAndDirection()
    {
        switch ($this->sortOption) {
            case 'oldest':
                $this->sortField = 'created_at';
                $this->sortDirection = 'asc';
                break;
            case 'nameAZ':
                $this->sortField = 'name';
                $this->sortDirection = 'asc';
                break;
            case 'nameZA':
                $this->sortField = 'name';
                $this->sortDirection = 'desc';
                break;
            case 'lastOnline':
                $this->sortField = 'last_online';
                $this->sortDirection = 'desc';
                break;
            default: // newest
                $this->sortField = 'created_at';
                $this->sortDirection = 'desc';
                break;
        }
    }

    /**
     * Clear all filters and reset to default values
     * This method is called when the user clicks the "Clear Filters" button
     */
    public function clearFilters()
    {
        $this->searchQuery = '';
        $this->sortOption = 'newest';
        $this->updateSortFieldAndDirection();
        $this->filterUserType = '';
        $this->filterBanStatus = '';
        $this->resetPage();
        
        // Emit an event to notify JavaScript that filters were cleared
        $this->dispatch('filtersCleared');
    }

    /**
     * Check if there are any active filters
     * Returns true if any filter is applied
     */
    public function hasActiveFilters()
    {
        return !empty($this->searchQuery) ||
            !empty($this->filterUserType) ||
            !empty($this->filterBanStatus) ||
            $this->sortOption !== 'newest';
    }

    /**
     * Check if the filter info row should be displayed
     * Returns true if there are active filters or users to display
     */
    public function showFilterInfo()
    {
        return $this->hasActiveFilters() || $this->getTotalUsers() > 0;
    }
}
