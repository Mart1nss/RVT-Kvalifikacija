<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuditLogService;
use App\Models\SentNotification;
use App\Models\NotificationRead;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = SentNotification::with(['sender', 'reads']);

            // Apply type filters
            if ($request->has('types') && !empty($request->types)) {
                $types = explode(',', $request->types);
                $validTypes = array_intersect($types, ['all', 'users', 'admins', 'self']);
                if (!empty($validTypes)) {
                    $query->whereIn('recipient_type', $validTypes);
                }
            }

            // Apply sorting
            $sortType = $request->input('sort', 'newest');
            if ($sortType === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Get paginated results with query string
            $notifications = $query->paginate(15)->withQueryString();

            // Get counts
            $totalCount = SentNotification::count();
            $filteredCount = $notifications->total();

            if ($request->ajax()) {
                // Prepare the HTML content
                $html = $filteredCount > 0
                    ? view('partials.notification-cards', ['notifications' => $notifications])->render()
                    : '<div class="empty-state"><p>No notifications found for the selected filters.</p></div>';

                // Prepare pagination HTML only if we have results
                $pagination = $filteredCount > 0
                    ? $notifications->links('pagination::tailwind')->render()
                    : '';

                return response()->json([
                    'html' => $html,
                    'pagination' => $pagination,
                    'total' => $totalCount,
                    'filteredCount' => $filteredCount
                ]);
            }

            return view('notifications', compact('notifications', 'totalCount', 'filteredCount'));
        } catch (\Exception $e) {
            \Log::error('Notification index error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'An error occurred while loading notifications: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'An error occurred while loading notifications');
        }
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'recipient_type' => 'required|in:all,users,admins,self'
        ]);

        try {
            // Store in sent notifications with recipient type
            $sentNotification = SentNotification::create([
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'recipient_type' => $request->recipient_type
            ]);

            // Get recipients based on type
            $users = $this->getRecipients($request->recipient_type);

            // Send notifications to recipients
            foreach ($users as $user) {
                $user->notify(new AdminBroadcastNotification($request->message, $sentNotification->id));
            }

            AuditLogService::log(
                "Sent notification",
                "notification",
                $request->message,
                null,
                "Notification to " . $request->recipient_type
            );

            return redirect()->back()->with('success', 'Notification sent successfully!');
        } catch (\Exception $e) {
            \Log::error('Error sending notification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send notification. Please try again.');
        }
    }

    private function getRecipients($type)
    {
        switch ($type) {
            case 'all':
                return User::all();
            case 'users':
                return User::where('usertype', '!=', 'admin')->get();
            case 'admins':
                return User::where('usertype', 'admin')->get();
            case 'self':
                return User::where('id', auth()->id())->get();
            default:
                return collect();
        }
    }

    public function deleteNotification($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);

            // Before deleting, check if we need to track the read status
            if ($notification->read_at && isset($notification->data['sent_notification_id'])) {
                NotificationRead::firstOrCreate([
                    'user_id' => auth()->id(),
                    'sent_notification_id' => $notification->data['sent_notification_id'],
                    'read_at' => $notification->read_at
                ]);
            }

            $notification->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to delete notification'], 500);
        }
    }

    public function deleteSentNotification($id)
    {
        try {
            $sentNotification = SentNotification::findOrFail($id);
            // Delete all related notifications
            DB::table('notifications')
                ->where('data->sent_notification_id', $id)
                ->delete();
            // Delete the sent notification
            $sentNotification->delete();

            return redirect()->back()->with('success', 'Notification deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting sent notification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete notification. Please try again.');
        }
    }

    public function deleteAllNotifications()
    {
        try {
            // Get all read notifications with sent_notification_id before deleting
            $readNotifications = auth()->user()->notifications()
                ->whereNotNull('read_at')
                ->get()
                ->filter(function ($notification) {
                    return isset($notification->data['sent_notification_id']);
                });

            // Track read status for each notification
            foreach ($readNotifications as $notification) {
                NotificationRead::firstOrCreate([
                    'user_id' => auth()->id(),
                    'sent_notification_id' => $notification->data['sent_notification_id'],
                    'read_at' => $notification->read_at
                ]);
            }

            auth()->user()->notifications()->delete();
            return redirect()->back()->with('success', 'All notifications deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting all notifications: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete notifications. Please try again.');
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $notifications = auth()->user()->unreadNotifications;

            foreach ($notifications as $notification) {
                if (isset($notification->data['sent_notification_id'])) {
                    NotificationRead::firstOrCreate([
                        'user_id' => auth()->id(),
                        'sent_notification_id' => $notification->data['sent_notification_id'],
                        'read_at' => now()
                    ]);
                }
            }

            $notifications->markAsRead();
            return redirect()->back()->with('success', 'All notifications marked as read!');
        } catch (\Exception $e) {
            \Log::error('Error marking notifications as read: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark notifications as read. Please try again.');
        }
    }

    public function getCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }
}
