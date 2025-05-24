<?php

namespace App\Models;

use App\Models\Favorite;
use App\Models\Review;
use App\Models\Ticket;
use App\Models\UserPreference;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumReply;
use App\Notifications\CustomResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\ReadBook;
use App\Models\SentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\AdminTicketsUnassigned;
use App\Models\TicketResponse;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'has_genre_preference_set',
        'last_read_book_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            Log::info("[User Model Event] User ID {$user->id} ({$user->name}) is being deleted.");

            // --- Logic for unassigning tickets if the deleting user is an admin ---
            if ($user->isAdmin()) {
                $ticketsAssignedToAdmin = Ticket::where('assigned_admin_id', $user->id)
                                                ->get();
                $unassignedCount = 0;

                if ($ticketsAssignedToAdmin->isNotEmpty()) {
                    Log::info("[User Model Event] Admin user ID {$user->id} ({$user->name}) is being deleted. Found {$ticketsAssignedToAdmin->count()} tickets assigned. Unassigning...");
                    foreach ($ticketsAssignedToAdmin as $ticketToUnassign) {
                        $ticketToUnassign->assigned_admin_id = null;
                        $ticketToUnassign->status = Ticket::STATUS_OPEN; // Re-open the ticket
                        $ticketToUnassign->save();
                        $unassignedCount++;
                    }
                }

                if ($unassignedCount > 0) {
                    // Pass the user model being deleted, not a fresh instance
                    $otherAdmins = User::where('usertype', 'admin')->where('id', '!=', $user->id)->get();
                    if ($otherAdmins->isNotEmpty()) {
                        Log::info("[User Model Event] Dispatching AdminTicketsUnassigned for deleted admin ID {$user->id}. Tickets unassigned: {$unassignedCount}. Reason: account_deleted_from_model_event.");
                        event(new AdminTicketsUnassigned($user, $unassignedCount, 'account_deleted_from_model_event'));
                    } else {
                        Log::info("[User Model Event] Admin ID {$user->id} deleted, {$unassignedCount} tickets unassigned, but no other admins to notify.");
                    }
                } else {
                     Log::info("[User Model Event] Admin ID {$user->id} deleted, no tickets were assigned to them to unassign.");
                }
            }
            // --- End logic for unassigning tickets ---

            // Tickets created by the user will have their user_id set to null by the database
            // due to onDelete('set null') constraint. Same for reviews, forums, forum_replies.
            // Ticket responses associated with the user will also have their user_id set to null.

            // Original logic: If the user being deleted is an admin, clean up their sent notifications
            if ($user->isAdmin()) {
                Log::info("[User Model Event] Admin user ID {$user->id} ({$user->name}) is being deleted. Cleaning up sent notifications they created.");
                $sentNotificationIdsByThisAdmin = SentNotification::where('sender_id', $user->id)
                                                                    ->pluck('id');

                if ($sentNotificationIdsByThisAdmin->isNotEmpty()) {
                    DB::table('notifications')
                        ->whereIn('data->sent_notification_id', $sentNotificationIdsByThisAdmin)
                        ->delete();
                    Log::info("[User Model Event] Deleted main notifications linked to SentNotifications by admin ID {$user->id}.");
                }
                // SentNotification records themselves and related NotificationRead records
                // are expected to be handled by DB cascading deletes if sender_id FK has ON DELETE CASCADE.
            }
        });
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function readLater()
    {
        return $this->hasMany(ReadLater::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function isAdmin()
    {
        return $this->usertype === 'admin';
    }

    public function userPreferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function preferredCategories()
    {
        return $this->belongsToMany(Category::class, 'user_preferences');
    }

    public function lastReadBook()
    {
        return $this->belongsTo(Product::class, 'last_read_book_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function bans()
    {
        return $this->hasMany(Ban::class);
    }

    public function activeBan()
    {
        return $this->hasOne(Ban::class)->active();
    }
    
    public function isBanned()
    {
        return $this->activeBan()->exists();
    }
    
    public function getBanReason()
    {
        if ($ban = $this->activeBan()->first()) {
            return $ban->reason;
        }
        
        return null;
    }

    public function createdBans()
    {
        return $this->hasMany(Ban::class, 'banned_by');
    }

    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function readBooks()
    {
        return $this->hasMany(ReadBook::class);
    }
    
    public function hasRead($productId)
    {
        return $this->readBooks()->where('product_id', $productId)->exists();
    }
}
