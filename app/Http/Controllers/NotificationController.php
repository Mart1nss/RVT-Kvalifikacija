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
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }
}
