<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuditLogService;
use App\Models\SentNotification;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function sendNotification(Request $request)
    {
        $request->validate(['message' => 'required']);

        // Store in sent notifications
        $sentNotification = SentNotification::create([
            'sender_id' => auth()->id(),
            'message' => $request->message
        ]);

        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new AdminBroadcastNotification($request->message, $sentNotification->id));
        }

        AuditLogService::log(
            "Sent notification",
            "notification",
            $request->message,
            null,
            "Global notification"
        );

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification sent!');
    }

    public function deleteNotification($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function deleteAllNotifications()
    {
        auth()->user()->notifications()->delete();
        return response()->json(['success' => true]);
    }

    public function deleteSentNotification($id)
    {
        $sentNotification = SentNotification::findOrFail($id);
        // Delete all related notifications
        \DB::table('notifications')
            ->where('data->sent_notification_id', $id)
            ->delete();
        // Delete the sent notification
        $sentNotification->delete();
        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function getCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }
}
