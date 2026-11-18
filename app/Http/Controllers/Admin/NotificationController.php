<?php

namespace App\Http\Controllers\Admin; // <--- WAJIB ADMIN

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = [];

            if (auth()->check()) {
                $dbNotifs = auth()->user()->unreadNotifications()->take(5)->get();
                foreach ($dbNotifs as $n) {
                    $notifications[] = [
                        'id' => $n->id,
                        'data' => [
                            'pesan' => $n->data['pesan'] ?? 'Notifikasi baru',
                            'url' => $n->data['url'] ?? '#',
                        ],
                        'created_at' => $n->created_at->toIso8601String(),
                        'is_db' => true
                    ];
                }
            }

            $pendingPesanan = Pesanan::where('order_status', 'pending')->count();
            if ($pendingPesanan > 0) {
                $notifications[] = [
                    'id' => 'pending_pesanan',
                    'data' => [
                        'pesan' => "Ada {$pendingPesanan} pesanan baru yang menunggu konfirmasi.",
                        'url' => route('admin.pesanan.index', ['status' => 'pending']),
                    ],
                    'created_at' => now()->toIso8601String(),
                    'is_db' => false
                ];
            }

            $pendingRefund = Refund::where('status_refund', 'pending')->count();
            if ($pendingRefund > 0) {
                $notifications[] = [
                    'id' => 'pending_refund',
                    'data' => [
                        'pesan' => "Ada {$pendingRefund} pengajuan refund yang perlu diproses.",
                        'url' => route('admin.refund.index', ['status' => 'pending']),
                    ],
                    'created_at' => now()->toIso8601String(),
                    'is_db' => false
                ];
            }

            return response()->json($notifications);

        } catch (\Exception $e) {
            Log::error('Notif Admin Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            auth()->user()->unreadNotifications()->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function readOne($id)
    {
        if (str_starts_with($id, 'pending_')) {
            return redirect()->back();
        }

        try {
            /** @var \Illuminate\Notifications\DatabaseNotification $notif */
            $notif = auth()->user()->notifications()->findOrFail($id);
            $notif->markAsRead();
            return redirect($notif->data['url']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal buka notif: ' . $e->getMessage());
        }
    }
}