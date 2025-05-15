<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Ban;
use App\Models\Ticket; // Added Ticket model
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Notification; // Added for sending notifications
use App\Notifications\TicketUnassignedNotification; // We will create this notification
use App\Notifications\UserRoleChangedNotification; // Added new notification
use Livewire\Component;
use Illuminate\Support\Facades\Log; // Added for logging

/**
 * UserShow Component
 * 
 * This Livewire component handles displaying and managing a single user's details:
 * - Viewing user information
 * - Changing user role (admin/user)
 * - Banning/unbanning users
 * - Deleting users
 */
class UserShow extends Component
{
    public $user;
    public $userId;
    public $showDeleteModal = false;
    public $showBanModal = false;
    public $banReason = '';

    /**
     * Initialize the component
     * This method is called when the component is first loaded
     * 
     * @param int $userId The ID of the user to display
     */
    public function mount($userId)
    {
        $this->userId = $userId;
        
        // Prevent admins from editing their own account
        if (auth()->id() == $userId) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot edit your own account for security reasons.'
                ]
            ]);
            
            return redirect()->route('user.management.livewire');
        }
        
        $this->loadUser();
    }

    /**
     * Load the user data from the database
     * This method fetches the user by ID or shows a 404 if not found
     */
    public function loadUser()
    {
        $this->user = User::findOrFail($this->userId);
    }

    /**
     * Render the component view
     * This method is called whenever the component needs to be re-rendered
     */
    public function render()
    {
        return view('livewire.users.user-show');
    }

    /**
     * Update the user's role (admin/user)
     * 
     * @param string $userType The new user type/role
     */
    public function updateUserType($userType)
    {
        Log::info("[UserShow] updateUserType called for user ID {$this->user->id} ({$this->user->name}) with new type '{$userType}'. Current actual usertype: {$this->user->usertype}. Auth User: " . auth()->id());

        // Double-check that admin is not editing their own account
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot change your own role for security reasons.'
                ]
            ]);
            return;
        }
        
        // Save the old role for logging purposes
        $oldRole = $this->user->usertype;
        
        // Update and save the user's role
        $this->user->usertype = $userType;
        $this->user->save();

        // If user was an admin and is no longer an admin, unassign their tickets
        if ($oldRole === 'admin' && $userType !== 'admin') {
            $adminUser = $this->user;
            Log::info("[UserShow] updateUserType: Admin role change condition met for user ID {$adminUser->id} ({$adminUser->name}). Old role: '{$oldRole}', New type: '{$userType}'. Unassigning tickets.");
            $ticketsToUnassign = Ticket::where('assigned_admin_id', $adminUser->id)->get();
            foreach ($ticketsToUnassign as $ticket) {
                $ticket->assigned_admin_id = null;
                $ticket->status = Ticket::STATUS_OPEN; // Re-open the ticket
                $ticket->save();
            }

            // Notify other admins
            $otherAdmins = User::where('usertype', 'admin')->where('id', '!=', $adminUser->id)->get();
            if ($otherAdmins->isNotEmpty()) {
                Log::info("[UserShow] updateUserType: Dispatching AdminTicketsUnassigned event for user ID {$adminUser->id}. Ticket count: " . count($ticketsToUnassign) . ". Reason: role_changed.");
                event(new \App\Events\AdminTicketsUnassigned($adminUser, count($ticketsToUnassign), 'role_changed'));
                $this->dispatch('alert', [
                    [
                        'type' => 'info',
                        'message' => "Tickets previously assigned to {$adminUser->name} have been unassigned due to role change."
                    ]
                ]);
            }
        }

        // Notify the user whose role was changed
        if ($oldRole !== $userType) { // Check if a role change actually occurred
            try {
                $this->user->notify(new UserRoleChangedNotification($this->user, $oldRole, $userType));
                Log::info("[UserShow] updateUserType: Sent UserRoleChangedNotification to user ID {$this->user->id} for role change from '{$oldRole}' to '{$userType}'.");
            } catch (\Exception $e) {
                Log::error("[UserShow] updateUserType: Failed to send UserRoleChangedNotification to user ID {$this->user->id}. Error: " . $e->getMessage());
            }
        }

        // Log the role change in the audit log for accountability
        AuditLogService::log(
            "Changed user role for",
            "user",
            "Changed {$this->user->name}'s role from '{$oldRole}' to '{$userType}'",
            $this->user->id,
            $this->user->name
        );

        // Show success message to the admin
        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User type for {$this->user->name} updated successfully."
            ]
        ]);
    }

    /**
     * Show the delete confirmation modal
     * This method is triggered when the admin clicks the "Delete User" button
     */
    public function confirmDelete()
    {
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        $this->showDeleteModal = true;
    }

    /**
     * Cancel the delete process and close the modal
     * This method is triggered when the admin clicks "Cancel" in the delete modal
     */
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    /**
     * Delete the user from the database
     * This method is triggered when the admin confirms deletion in the modal
     */
    public function deleteUser()
    {
        Log::info("[UserShow] deleteUser called for user ID {$this->user->id} ({$this->user->name}). Auth User: " . auth()->id());
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account for security reasons.'
                ]
            ]);
            return;
        }

        AuditLogService::log(
            "Deleted user",
            "user",
            "Deleted user account",
            $this->user->id,
            $this->user->name
        );

        $userName = $this->user->name;
        // The logic for unassigning tickets and dispatching AdminTicketsUnassigned event
        // has been moved to the User model's 'deleting' event listener in User::booted().
        // This ensures it runs globally whenever a user is deleted.
        
        // Delete the user from the database
        // This will trigger the 'deleting' event in the User model.
        $this->user->delete();
        
        $this->showDeleteModal = false;

        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$userName} deleted successfully."
            ]
        ]);

        return redirect()->route('user.management.livewire');
    }

    /**
     * Show the ban confirmation modal
     * This method is triggered when the admin clicks the "Ban User" button
     */
    public function confirmBan()
    {
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot ban your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        $this->showBanModal = true;
    }

    /**
     * Cancel the ban process and close the modal
     * This method is triggered when the admin clicks "Cancel" in the ban modal
     */
    public function cancelBan()
    {
        $this->showBanModal = false;
        $this->banReason = '';
    }

    /**
     * Ban the user
     * This method is triggered when the admin confirms the ban in the modal
     * It creates a new Ban record and logs the action
     */
    public function banUser()
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot ban your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        if (empty($this->banReason)) {
            $this->addError('banReason', 'A reason for the ban is required.');
            return;
        }

        Ban::create([
            'user_id' => $this->user->id,
            'reason' => $this->banReason,
            'banned_by' => auth()->id(),
            'is_active' => true
        ]);

        AuditLogService::log(
            "Banned user",
            "user",
            "Banned user account for: {$this->banReason}",
            $this->user->id,
            $this->user->name
        );

        $this->showBanModal = false;
        $this->banReason = '';

        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$this->user->name} has been banned."
            ]
        ]);
        
        $this->loadUser();
    }

    /**
     * Unban the user
     * This method is triggered when the admin clicks the "Unban User" button
     * It deactivates all active bans for the user and logs the action
     */
    public function unbanUser()
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        Ban::where('user_id', $this->user->id)->delete();

        AuditLogService::log(
            "Unbanned user",
            "user",
            "Unbanned user account",
            $this->user->id,
            $this->user->name
        );

        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$this->user->name} has been unbanned."
            ]
        ]);
        
        $this->loadUser();
    }
}
