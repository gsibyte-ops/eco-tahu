<?php

namespace App\Support;

use App\Models\Pesanan;
use App\Models\User;

class FirstOrderPromo
{
    /**
     * Ambil konfigurasi promo pembelian pertama dari config/toko.php.
     */
    public static function config(): array
    {
        $cfg = config('toko.promo_order_pertama', []);

        return [
            'aktif'       => (bool) ($cfg['aktif'] ?? true),
            'persen'      => (int) ($cfg['persen'] ?? 10),
            'max_diskon'  => (int) ($cfg['max_diskon'] ?? 5000),
            'min_belanja' => (int) ($cfg['min_belanja'] ?? 20000),
        ];
    }

    /**
     * Normalisasi nomor telepon ke format digit 628... agar 08xx, +628xx, 628xx seragam.
     */
    public static function normalizePhone(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if (strlen($digits) < 8) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    /**
     * Evaluasi kelayakan dan nominal diskon pembelian pertama secara terpusat di server.
     */
    public static function evaluate(?User $user, int|float $subtotal, ?string $recipientPhone = null): array
    {
        $cfg = self::config();
        $aktif = $cfg['aktif'];
        $persen = max(0, $cfg['persen']);
        $maxDiskon = max(0, $cfg['max_diskon']);
        $minBelanja = max(0, $cfg['min_belanja']);
        $label = "Diskon {$persen}% Order Pertama";

        $base = [
            'aktif'            => $aktif,
            'eligible'         => false,
            'applied'          => false,
            'persen'           => $persen,
            'max_diskon'       => $maxDiskon,
            'min_belanja'      => $minBelanja,
            'diskon'           => 0,
            'kurang_minimal'   => 0,
            'reason'           => null,
            'blocked_by_phone' => false,
            'label'            => $label,
        ];

        if (!$aktif || $persen <= 0) {
            $base['reason'] = 'Promo pembelian pertama sedang tidak aktif.';
            return $base;
        }

        if (!$user || $user->isAdmin()) {
            $base['reason'] = 'Promo khusus untuk pelanggan baru.';
            return $base;
        }

        // 1. Cek riwayat pesanan akun (pesanan dibatalkan tidak menghanguskan hak promo)
        $hasActiveOrder = Pesanan::where('user_id', $user->id)
            ->where('order_status', '!=', 'dibatalkan')
            ->exists();

        if ($hasActiveOrder) {
            $base['reason'] = 'Promo hanya berlaku untuk pembelian pertama.';
            return $base;
        }

        // 2. Anti-abuse: cek nomor HP penerima & nomor HP profil akun
        $candidatePhones = array_values(array_unique(array_filter([
            self::normalizePhone($recipientPhone),
            self::normalizePhone($user->no_telepon),
        ])));

        if (!empty($candidatePhones)) {
            $usedByRecipientPhone = Pesanan::where('order_status', '!=', 'dibatalkan')
                ->where('diskon', '>', 0)
                ->whereIn('no_telepon_penerima', $candidatePhones)
                ->exists();

            if ($usedByRecipientPhone) {
                $base['blocked_by_phone'] = true;
                $base['reason'] = 'Nomor HP ini sudah pernah menggunakan promo pembelian pertama.';
                return $base;
            }

            // Cek silang terhadap nomor profil akun lain yang pernah memakai promo
            $otherPromoUsers = User::where('id', '!=', $user->id)
                ->whereNotNull('no_telepon')
                ->whereHas('pesanan', function ($q) {
                    $q->where('order_status', '!=', 'dibatalkan')
                      ->where('diskon', '>', 0);
                })
                ->pluck('no_telepon');

            foreach ($otherPromoUsers as $otherRawPhone) {
                $otherNorm = self::normalizePhone($otherRawPhone);
                if ($otherNorm !== null && in_array($otherNorm, $candidatePhones, true)) {
                    $base['blocked_by_phone'] = true;
                    $base['reason'] = 'Nomor HP ini sudah pernah menggunakan promo pembelian pertama.';
                    return $base;
                }
            }
        }

        // 3. Hitung potongan hanya dari subtotal produk (ongkir tidak dipotong)
        $subtotalInt = max(0, (int) round($subtotal));
        $base['eligible'] = true;

        if ($subtotalInt < $minBelanja) {
            $base['kurang_minimal'] = $minBelanja - $subtotalInt;
            $base['reason'] = 'Tambah belanja Rp ' . number_format($base['kurang_minimal'], 0, ',', '.')
                . ' lagi untuk aktifkan ' . $label . '.';
            return $base;
        }

        $rawDiscount = (int) round(($subtotalInt * $persen) / 100);
        $diskon = min($rawDiscount, $maxDiskon);

        $base['diskon'] = $diskon;
        $base['applied'] = $diskon > 0;
        $base['kurang_minimal'] = 0;

        return $base;
    }
}
