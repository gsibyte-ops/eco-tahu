<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get list of user notifications.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->take(30)->get();

        $formatted = $notifications->map(function ($notif) {
            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
            return [
                'id'         => $notif->id,
                'title'      => 'Info Pesanan',
                'pesan'      => $data['pesan'] ?? 'Notifikasi baru',
                'url'        => $data['url'] ?? null,
                'is_read'    => $notif->read_at !== null,
                'read_at'    => $notif->read_at?->toIso8601String(),
                'created_at' => $notif->created_at?->diffForHumans(),
                'timestamp'  => $notif->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'unread_count' => $user->unreadNotifications()->count(),
            'data' => $formatted,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi ditandai sudah dibaca.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notifikasi tidak ditemukan.',
        ], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }
}
