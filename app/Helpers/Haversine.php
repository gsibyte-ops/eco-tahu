<?php

namespace App\Helpers;

class Haversine
{
    /**
     * Hitung jarak (km) antara 2 titik koordinat pakai rumus Haversine.
     */
    public static function distance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo   = deg2rad($lat2);
        $lngTo   = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2
           + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Hitung ongkir dari jarak (km).
     * Rumus: max(ceil(jarak) * per_km, minimal)
     */
    public static function hitungOngkir(float $jarakKm, ?int $perKm = null, ?int $minimal = null): int
    {
        $perKm   = $perKm   ?? (int) config('toko.ongkir_per_km', 2500);
        $minimal = $minimal ?? (int) config('toko.ongkir_minimal', 5000);

        if ($jarakKm <= 0) {
            return 0;
        }

        $ongkir = (int) ceil($jarakKm) * $perKm;

        return max($ongkir, $minimal);
    }
}