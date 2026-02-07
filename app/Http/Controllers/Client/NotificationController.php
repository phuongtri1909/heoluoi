<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function show(Notification $notification)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $canSee = Notification::forUser($userId)->where('notifications.id', $notification->id)->exists();
        if (!$canSee) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json([
            'id' => $notification->id,
            'title' => $notification->title,
            'body' => $notification->body,
            'created_at' => $notification->created_at->format('d/m/Y H:i'),
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['ok' => false], 403);
        }

        $canSee = Notification::forUser($userId)->where('notifications.id', $notification->id)->exists();
        if (!$canSee) {
            return response()->json(['ok' => false], 404);
        }

        DB::table('notification_user')->updateOrInsert(
            [
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ],
            [
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }
}
