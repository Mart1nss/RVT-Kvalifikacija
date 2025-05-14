<div>
    <div class="text-container" style="background: transparent; padding: 0;">
        <h1 class="text-container-title">USER DETAILS</h1>
    </div>

    <div class="user-details-container">
        <!-- User information card -->
        <div class="user-info-card">
            <div class="user-header">
                <h2>{{ $user->name }}</h2>
                <!-- Dynamic badge that changes color based on user role -->
                <span class="user-role {{ $user->usertype == 'admin' ? 'admin-badge' : 'user-badge' }}">
                    {{ ucfirst($user->usertype) }}
                </span>
            </div>

            <!-- Basic user details section -->
            <div class="user-details">
                <div class="detail-row">
                    <span class="detail-label">ID:</span>
                    <span class="detail-value">{{ $user->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Last Online:</span>
                    <span class="detail-value">{{ $user->last_online ? $user->last_online->diffForHumans() : 'Never' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Created At:</span>
                    <span class="detail-value">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Updated At:</span>
                    <span class="detail-value">{{ $user->updated_at->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- User management actions section -->
            <div class="user-actions">
                <div class="action-section">
                    <h3>Change User Role</h3>
                    <div class="role-selector">
                        <select wire:change="updateUserType($event.target.value)" class="usertype-select">
                            <option value="user" {{ $user->usertype == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->usertype == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <!-- Danger zone section for ban/unban and delete actions -->
                <div class="action-section">
                    <h3>Danger Zone</h3>
                    @if(!$user->isBanned())
                        <button type="button" class="btn btn-danger btn-md" wire:click="confirmBan">
                            <i class='bx bx-shield-x'></i> Ban User
                        </button>
                    <!-- Otherwise show unban button and ban information -->
                    @else
                        <button type="button" class="btn-unban" wire:click="unbanUser">
                            <i class='bx bx-shield-quarter'></i> Unban User
                        </button>
                        <div class="ban-info">
                            @php
                                $activeBan = $user->activeBan()->first();
                            @endphp
                            <p><strong>Banned at:</strong> {{ $activeBan->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Reason:</strong> {{ $activeBan->reason }}</p>
                            @if($activeBan->admin)
                                <p><strong>Banned by:</strong> {{ $activeBan->admin->name }}</p>
                            @endif
                        </div>
                    @endif
                    <button type="button" class="btn btn-danger btn-md" wire:click="confirmDelete">
                        <i class='bx bx-trash'></i> Delete User
                    </button>
                </div>
            </div>
        </div>

        <!-- User activity statistics card -->
        <div class="user-activity-card">
            <h3>User Activity</h3>
            <div class="activity-stats">
                <div class="stat-item">
                    <span class="stat-label">Reviews:</span>
                    <span class="stat-value">{{ $user->reviews->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Favorites:</span>
                    <span class="stat-value">{{ $user->favorites->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Notes:</span>
                    <span class="stat-value">{{ $user->notes->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Support Tickets:</span>
                    <span class="stat-value">{{ $user->tickets->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Forums Created:</span>
                    <span class="stat-value">{{ $user->forums->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Forum Replies:</span>
                    <span class="stat-value">{{ $user->forumReplies->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal - only shown when showDeleteModal is true -->
    @if($showDeleteModal)
    <div class="delete-confirmation-modal active">
        <div class="delete-confirmation-content">
            <div class="delete-confirmation-header">
                <h2>Delete User</h2>
            </div>
            <div class="delete-confirmation-body">
                <p>Are you sure you want to delete user <span class="text-alert">{{ $user->name }}</span>?</p>
                <p class="delete-confirmation-text">This action cannot be undone.</p>
            </div>
            <div class="delete-confirmation-footer">
                <button type="button" class="btn btn-ghost btn-md" wire:click="cancelDelete">Cancel</button>
                <button type="button" class="btn btn-danger btn-md" wire:click="deleteUser">Delete</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Ban Confirmation Modal - only shown when showBanModal is true -->
    @if($showBanModal)
    <div class="ban-confirmation-modal active">
        <div class="ban-confirmation-content">
            <div class="ban-confirmation-header">
                <h2>Ban User</h2>
            </div>
            <div class="ban-confirmation-body">
                <p>Are you sure you want to ban user <span class="text-alert">{{ $user->name }}</span>?</p>
                <p>Banned users will not be able to log in to the website.</p>
                <div class="ban-reason-input">
                    <label for="banReason">Reason for ban:</label>
                    <textarea id="banReason" wire:model="banReason" rows="3" placeholder="Enter reason for banning this user..." required></textarea>
                    @error('banReason') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="ban-confirmation-footer">
                <button type="button" class="btn btn-ghost btn-md" wire:click="cancelBan">Cancel</button>
                <button type="button" class="btn btn-danger btn-md" wire:click="banUser">Ban User</button>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        .error {
            color: #dc2626;
        }
    </style>
</div>
