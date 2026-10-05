<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pesanan;
use App\Models\Refund;
use App\Models\ProdukTahu;
use App\Models\Limbah;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share global data ke layout admin — buat badge & notif
        View::composer('layouts.admin', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $pesananPending = Pesanan::where('order_status', 'pending')->count();
            $refundPending  = Refund::where('status_refund', 'pending')->count();
            $stokMenipis    = ProdukTahu::where('stok', '<=', 10)->count()
                            + Limbah::where('stok', '<=', 10)->count();

            // Notif pesanan baru (status pending), 3 terbaru
            $notifPesanan = Pesanan::with('user')
                ->where('order_status', 'pending')
                ->orderByDesc('tanggal_order')
                ->limit(3)
                ->get()
                ->map(fn ($p) => [
                    'type'     => 'pesanan',
                    'title'    => 'Pesanan Baru',
                    'subtitle' => '#' . $p->kode_pesanan . ' · ' . ($p->user->username ?? '-'),
                    'amount'   => 'Rp ' . number_format($p->total_harga, 0, ',', '.'),
                    'time'     => $p->tanggal_order?->diffForHumans() ?? '-',
                    'url'      => route('admin.pesanan.show', $p->id),
                    'icon'     => 'cart',
                ]);

            // Notif refund baru (status pending), 3 terbaru
            $notifRefund = Refund::with('pesanan.user')
                ->where('status_refund', 'pending')
                ->orderByDesc('tanggal_refund')
                ->limit(3)
                ->get()
                ->map(fn ($r) => [
                    'type'     => 'refund',
                    'title'    => 'Pengajuan Refund',
                    'subtitle' => '#' . ($r->pesanan->kode_pesanan ?? '-') . ' · ' . ($r->pesanan->user->username ?? '-'),
                    'amount'   => 'Rp ' . number_format($r->nominal_refund, 0, ',', '.'),
                    'time'     => $r->tanggal_refund?->diffForHumans() ?? '-',
                    'url'      => route('admin.refund.index'),
                    'icon'     => 'refund',
                ]);

            $allNotifs = $notifPesanan->concat($notifRefund)
                ->sortByDesc('time')
                ->take(5)
                ->values();

            $view->with([
                'globalPesananPending' => $pesananPending,
                'globalRefundPending'  => $refundPending,
                'globalStokMenipis'    => $stokMenipis,
                'globalNotifs'         => $allNotifs,
                'globalNotifTotal'     => $pesananPending + $refundPending,
            ]);
        });
    }
}