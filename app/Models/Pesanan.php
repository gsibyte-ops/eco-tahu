<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'kode_pesanan',
        'subtotal',
        'diskon',
        'ongkir',
        'jarak_km',
        'lat_tujuan',
        'lng_tujuan',
        'lat_kurir',
        'lng_kurir',
        'lokasi_updated_at',
        'pakai_pin',
        'total_harga',
        'payment_method',
        'delivery_type',
        'bank_tujuan',
        'va_number',
        'payment_status',
        'order_status',
        'estimasi_menit',
        'estimasi_tiba',
        'status_kurir',
        'alasan_batal',
        'alamat_pengiriman',
        'no_telepon_penerima',
        'kecamatan',
        'catatan',
        'tanggal_order',
        'expired_at',
    ];

    protected $appends = ['status_kurir_label'];

    protected $casts = [
        'tanggal_order'     => 'datetime',
        'expired_at'        => 'datetime',
        'estimasi_tiba'     => 'datetime',
        'lokasi_updated_at' => 'datetime',
        'estimasi_menit'    => 'integer',
        'diskon'            => 'float',
        'jarak_km'          => 'float',
        'lat_tujuan'        => 'float',
        'lng_tujuan'        => 'float',
        'lat_kurir'         => 'float',
        'lng_kurir'         => 'float',
        'pakai_pin'         => 'boolean',
        'total_harga'       => 'float', // TAMBAHAN: Biar jadi angka
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at
            && $this->expired_at->isPast()
            && $this->payment_status === 'pending'
            && $this->order_status === 'pending';
    }

    public function getDeliveryLabelAttribute(): string
    {
        if ($this->delivery_type === 'pickup') {
            return 'Ambil di Tempat';
        }
        return 'Diantar';
    }

    public static function courierStatusLabels(): array
    {
        return [
            'menyiapkan'      => 'Admin sedang menyiapkan pesanan',
            'menuju_lokasi'   => 'Kurir sedang dalam perjalanan menuju lokasimu',
            'hampir_sampai'   => 'Kurir sudah dekat di sekitar lokasimu (±5 menit lagi)',
            'sudah_di_lokasi' => 'Kurir sudah tiba di lokasi tujuan',
            'siap_diambil'    => 'Pesanan sudah siap diambil di lokasi EcoTahu',
        ];
    }

    public static function defaultEtaMinutes(self $pesanan): int
    {
        if ($pesanan->delivery_type === 'pickup') {
            return 15;
        }
        $km = (float) ($pesanan->jarak_km ?? 2);
        return max(15, min(120, 15 + (int) ceil($km * 3)));
    }

    public function getStatusKurirLabelAttribute(): ?string
    {
        if (!$this->status_kurir) {
            return null;
        }
        return self::courierStatusLabels()[$this->status_kurir] ?? ucfirst(str_replace('_', ' ', $this->status_kurir));
    }

    public function isTrackingActive(): bool
    {
        return $this->order_status === 'dikirim'
            && $this->lat_kurir !== null
            && $this->lng_kurir !== null
            && $this->lokasi_updated_at !== null
            && $this->lokasi_updated_at->diffInMinutes(now()) < 5;
    }
}