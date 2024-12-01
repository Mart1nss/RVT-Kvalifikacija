<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }
}

