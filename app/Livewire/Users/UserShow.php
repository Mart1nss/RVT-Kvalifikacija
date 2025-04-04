<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Services\AuditLogService;
use Livewire\Component;

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
    // Public properties that can be bound to the UI
    public $user;                // Stores the user model being viewed
    public $userId;              // Stores the ID of the user being viewed
    public $showDeleteModal = false; // Controls visibility of delete confirmation modal
    public $showBanModal = false;    // Controls visibility of ban confirmation modal
    public $banReason = '';          // Stores the reason for banning a user

    /**
     * Initialize the component
     * This method is called when the component is first loaded
     * 
     * @param int $userId The ID of the user to display
     */
    public function mount($userId)
    {
        $this->userId = $userId;
        
        // Security check: Prevent admins from editing their own account
        // This prevents accidental role changes or self-banning
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
        // Double-check that admin is not deleting their own account
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        // Show the delete confirmation modal
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
        // Check if the current user has admin privileges
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        // Final security check to prevent self-deletion
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account for security reasons.'
                ]
            ]);
            return;
        }

        // Log the deletion in the audit log for accountability
        AuditLogService::log(
            "Deleted user",
            "user",
            "Deleted user account",
            $this->user->id,
            $this->user->name
        );

        // Store the name before deletion for the success message
        $userName = $this->user->name;
        
        // Delete the user from the database
        $this->user->delete();
        
        // Close the modal
        $this->showDeleteModal = false;

        // Show success message to the admin
        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$userName} deleted successfully."
            ]
        ]);

        // Redirect to user management page after deletion
        return redirect()->route('user.management.livewire');
    }

    /**
     * Show the ban confirmation modal
     * This method is triggered when the admin clicks the "Ban User" button
     */
    public function confirmBan()
    {
        // Double-check that admin is not banning their own account
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot ban your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        // Show the ban confirmation modal
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
     * It sets the ban-related fields and logs the action
     */
    public function banUser()
    {
        // Check if the current user has admin privileges
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        // Final security check to prevent self-banning
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot ban your own account for security reasons.'
                ]
            ]);
            return;
        }

        // Set ban-related fields in the user record
        $this->user->is_banned = true;
        $this->user->banned_at = now();
        $this->user->ban_reason = $this->banReason;
        $this->user->banned_by = auth()->id();
        $this->user->save();

        // Log the ban action in the audit log for accountability
        AuditLogService::log(
            "Banned user",
            "user",
            "Banned user account" . ($this->banReason ? " for: {$this->banReason}" : ""),
            $this->user->id,
            $this->user->name
        );

        // Reset modal state
        $this->showBanModal = false;
        $this->banReason = '';

        // Show success message to the admin
        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$this->user->name} has been banned."
            ]
        ]);
    }

    /**
     * Unban the user
     * This method is triggered when the admin clicks the "Unban User" button
     * It clears the ban-related fields and logs the action
     */
    public function unbanUser()
    {
        // Check if the current user has admin privileges
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        // Clear all ban-related fields in the user record
        $this->user->is_banned = false;
        $this->user->banned_at = null;
        $this->user->ban_reason = null;
        $this->user->banned_by = null;
        $this->user->save();

        // Log the unban action in the audit log for accountability
        AuditLogService::log(
            "Unbanned user",
            "user",
            "Unbanned user account",
            $this->user->id,
            $this->user->name
        );

        // Show success message to the admin
        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$this->user->name} has been unbanned."
            ]
        ]);
    }
} 