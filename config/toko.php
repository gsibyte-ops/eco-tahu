<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lokasi Toko (Pabrik Tahu)
    |--------------------------------------------------------------------------
    */
    'lat' => -8.161482,
    'lng' => 113.724290,

    'nama'   => 'Pabrik Tahu EcoTahu',
    'alamat' => 'Lingkungan Panji, Tegalgede, Kec. Sumbersari, Kabupaten Jember, Jawa Timur 68124',

    /*
    |--------------------------------------------------------------------------
    | Tarif Ongkir
    |--------------------------------------------------------------------------
    | ongkir = max(ceil(jarak_km) * per_km, minimal)
    */
    'ongkir_per_km'  => 2500,
    'ongkir_minimal' => 5000,

    /*
    |--------------------------------------------------------------------------
    | Radius Jangkauan (km)
    |--------------------------------------------------------------------------
    */
    'radius_maks_km' => 10,

    /*
    |--------------------------------------------------------------------------
    | Kecamatan Jember (31 kecamatan)
    |--------------------------------------------------------------------------
    | Koordinat di sini adalah TITIK TENGAH kecamatan (aproksimasi).
    | Dipakai sebagai fallback kalau user nggak pin lokasi di peta.
    |
    | Kalau ada yang kurang akurat, tinggal edit. Cek di Google Maps:
    | klik kanan lokasi → "What's here?" → copy koordinat.
    */
    'kecamatan' => [
        'Ajung'        => ['lat' => -8.2167, 'lng' => 113.6500],
        'Ambulu'       => ['lat' => -8.3500, 'lng' => 113.6000],
        'Arjasa'       => ['lat' => -8.1200, 'lng' => 113.8300],
        'Balung'       => ['lat' => -8.2700, 'lng' => 113.5300],
        'Bangsalsari'  => ['lat' => -8.2200, 'lng' => 113.5200],
        'Gumukmas'     => ['lat' => -8.3200, 'lng' => 113.4100],
        'Jelbuk'       => ['lat' => -8.0500, 'lng' => 113.8000],
        'Jenggawah'    => ['lat' => -8.2700, 'lng' => 113.6500],
        'Jombang'      => ['lat' => -8.2100, 'lng' => 113.7000],
        'Kalisat'      => ['lat' => -8.1300, 'lng' => 113.8000],
        'Kaliwates'    => ['lat' => -8.1800, 'lng' => 113.6800],
        'Kencong'      => ['lat' => -8.2700, 'lng' => 113.3700],
        'Ledokombo'    => ['lat' => -8.1400, 'lng' => 113.8600],
        'Mayang'       => ['lat' => -8.1800, 'lng' => 113.7800],
        'Mumbulsari'   => ['lat' => -8.2400, 'lng' => 113.7300],
        'Pakusari'     => ['lat' => -8.1500, 'lng' => 113.7800],
        'Panti'        => ['lat' => -8.0800, 'lng' => 113.6300],
        'Patrang'      => ['lat' => -8.1350, 'lng' => 113.7150],
        'Puger'        => ['lat' => -8.2700, 'lng' => 113.4500],
        'Rambipuji'    => ['lat' => -8.2000, 'lng' => 113.6200],
        'Semboro'      => ['lat' => -8.2200, 'lng' => 113.4400],
        'Silo'         => ['lat' => -8.2500, 'lng' => 113.8500],
        'Sukorambi'    => ['lat' => -8.1400, 'lng' => 113.6800],
        'Sukowono'     => ['lat' => -8.1000, 'lng' => 113.8400],
        'Sumberbaru'   => ['lat' => -8.1300, 'lng' => 113.4000],
        'Sumberjambe'  => ['lat' => -8.0800, 'lng' => 113.7800],
        'Sumbersari'   => ['lat' => -8.1680, 'lng' => 113.7150],
        'Tanggul'      => ['lat' => -8.1600, 'lng' => 113.4600],
        'Tempurejo'    => ['lat' => -8.3200, 'lng' => 113.7300],
        'Umbulsari'    => ['lat' => -8.2500, 'lng' => 113.4500],
        'Wuluhan'      => ['lat' => -8.3400, 'lng' => 113.5300],
    ],
];