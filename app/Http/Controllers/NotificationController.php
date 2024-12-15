<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{

    public function sendNotification(Request $request)
    {
        $request->validate(['message' => 'required']);


        $users = User::all();
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => $request->message
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification sent!');
    }


    public function markAsRead(Request $request, Notification $notification)
    {
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        try {
            auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return back();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to mark notifications as read.');
        }
    }

    public function getCount()
    {
        try {
            $count = auth()->user()->notifications()->where('is_read', false)->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
