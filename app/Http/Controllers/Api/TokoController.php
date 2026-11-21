<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Haversine;
use App\Http\Controllers\Controller;
use App\Support\FirstOrderPromo;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    /**
     * Format a 628... WhatsApp number to 08...-....-.... display format if needed.
     */
    private function formatWhatsAppDisplay(string $whatsapp, ?string $configuredDisplay = null): string
    {
        if (!empty($configuredDisplay)) {
            return $configuredDisplay;
        }

        $digits = preg_replace('/\D+/', '', $whatsapp) ?? '';
        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }

        if (strlen($digits) >= 10) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8);
        }

        return $digits ?: $whatsapp;
    }

    /**
     * Get store information and kecamatan list.
     */
    public function info()
    {
        $toko = config('toko');
        $whatsapp = (string) ($toko['whatsapp'] ?? '6283833722986');

        return response()->json([
            'status' => 'success',
            'data' => [
                'nama'             => $toko['nama'] ?? 'Pabrik Tahu EcoTahu',
                'alamat'           => $toko['alamat'] ?? '',
                'whatsapp'         => $whatsapp,
                'whatsapp_display' => $this->formatWhatsAppDisplay($whatsapp, $toko['whatsapp_display'] ?? null),
                'email'            => $toko['email'] ?? 'hello@ecotahu.id',
                'jam_operasional'  => $toko['jam_operasional'] ?? 'Setiap Hari, 06.00 – 17.00 WIB',
                'sosmed'           => $toko['sosmed'] ?? [
                    'instagram' => [
                        'label' => '@jbr.iel',
                        'url'   => 'https://www.instagram.com/jbr.iel',
                    ],
                    'tiktok' => [
                        'label' => '@jbr.iel',
                        'url'   => 'https://www.tiktok.com/@jbr.iel',
                    ],
                    'facebook' => [
                        'label' => 'Zeki Zaki',
                        'url'   => 'https://www.facebook.com/share/19vBvyrxDe/',
                    ],
                ],
                'lat'              => (float) ($toko['lat'] ?? -8.161482),
                'lng'              => (float) ($toko['lng'] ?? 113.724290),
                'ongkir_per_km'    => (int) ($toko['ongkir_per_km'] ?? 2500),
                'ongkir_minimal'   => (int) ($toko['ongkir_minimal'] ?? 5000),
                'radius_maks_km'      => (float) ($toko['radius_maks_km'] ?? 10),
                'minimal_delivery'    => (int) ($toko['minimal_delivery'] ?? 15000),
                'promo_order_pertama' => FirstOrderPromo::config(),
                'kecamatan'           => $toko['kecamatan'] ?? [],
            ],
        ]);
    }

    /**
     * Evaluasi promo pembelian pertama untuk user terautentikasi.
     */
    public function cekPromo(Request $request)
    {
        $subtotal = (float) $request->query('subtotal', 0);
        $phone = $request->query('no_telepon', $request->user()?->no_telepon);

        return response()->json([
            'status' => 'success',
            'data'   => FirstOrderPromo::evaluate($request->user(), $subtotal, $phone),
        ]);
    }

    /**
     * Calculate distance and shipping cost based on coordinates.
     */
    public function hitungOngkir(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $latToko = (float) config('toko.lat', -8.161482);
        $lngToko = (float) config('toko.lng', 113.724290);
        $radiusMaks = (float) config('toko.radius_maks_km', 10);

        $jarak = Haversine::distance($latToko, $lngToko, (float) $request->lat, (float) $request->lng);
        $inRadius = $jarak <= $radiusMaks;
        $ongkir = $inRadius ? Haversine::hitungOngkir($jarak) : 0;

        return response()->json([
            'status'     => 'success',
            'jarak_km'   => $jarak,
            'ongkir'     => $ongkir,
            'in_radius'  => $inRadius,
            'radius_maks'=> $radiusMaks,
            'message'    => $inRadius ? 'Dalam jangkauan pengiriman' : "Di luar jangkauan pengiriman (maks {$radiusMaks} km)",
        ]);
    }
}
