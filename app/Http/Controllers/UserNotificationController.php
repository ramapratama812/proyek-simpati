<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function markRead(UserNotification $notification)
    {
        $user = auth()->user();

        // pastikan notif ini memang milik user yg login
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        if (is_null($notification->read_at)) {
            $notification->read_at = now();
            $notification->save();
        }

        return back();
    }

    public function markUnread(UserNotification $notification)
    {
        $user = auth()->user();

        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        if (! is_null($notification->read_at)) {
            $notification->read_at = null;
            $notification->save();
        }

        return back();
    }
}
