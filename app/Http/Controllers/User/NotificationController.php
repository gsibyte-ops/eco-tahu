<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->unreadNotifications()->take(5)->get();
        return response()->json($notifications);
    }

    public function markAllAsRead()
    {
        // Pake query update biar VS Code gak merah
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function readOne($id)
    {
        /** @var \Illuminate\Notifications\DatabaseNotification $notif */
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();
        return redirect($notif->data['url']);
    }
}