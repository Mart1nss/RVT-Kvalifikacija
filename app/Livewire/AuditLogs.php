<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class AuditLogs extends Component
{
    use WithPagination;

    // Set the theme for pagination
    protected $paginationTheme = 'tailwind';

    // Filter properties
    public $actionType = 'all';
    public $adminId = 'all';

    // Reset pagination when filters change
    public function updatedActionType()
    {
        $this->resetPage();
    }

    public function updatedAdminId()
    {
        $this->resetPage();
    }

    // Clear all filters
    public function clearFilters()
    {
        $this->actionType = 'all';
        $this->adminId = 'all';
        $this->resetPage();
        $this->dispatch('filtersCleared');
    }

    public function render()
    {
        try {
            // Get valid admin IDs
            $adminIds = User::where('usertype', 'admin')->pluck('id')->toArray();

            // Start building the query
            $logsQuery = AuditLog::with('admin')
                ->whereIn('admin_id', $adminIds);

            // Apply action type filter
            if ($this->actionType !== 'all') {
                $logsQuery->where('action_type', $this->actionType);
            }

            // Apply admin filter
            if ($this->adminId !== 'all') {
                $logsQuery->where('admin_id', $this->adminId);
            }

            // Get the logs with pagination
            $logs = $logsQuery->orderBy('created_at', 'desc')
                ->paginate(15);

            // Get all admins for the dropdown
            $admins = User::where('usertype', 'admin')->get();

            // Check for orphaned logs (those with invalid admin IDs)
            $orphanedCount = AuditLog::whereNotIn('admin_id', $adminIds)->count();
            if ($orphanedCount > 0) {
                Log::warning("Found {$orphanedCount} audit logs with invalid admin IDs");
            }

            return view('livewire.audit-logs', [
                'logs' => $logs,
                'admins' => $admins
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving audit logs: ' . $e->getMessage());

            return view('livewire.audit-logs', [
                'logs' => collect([]),
                'admins' => User::where('usertype', 'admin')->get(),
                'error' => 'Error retrieving audit logs. Please try again later.'
            ]);
        }
    }
}
