<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanan', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('alamat_pengiriman');
            }
            if (! Schema::hasColumn('pesanan', 'lat_tujuan')) {
                $table->decimal('lat_tujuan', 10, 8)->nullable()->after('jarak_km');
            }
            if (! Schema::hasColumn('pesanan', 'lng_tujuan')) {
                $table->decimal('lng_tujuan', 11, 8)->nullable()->after('lat_tujuan');
            }
            if (! Schema::hasColumn('pesanan', 'pakai_pin')) {
                $table->boolean('pakai_pin')->default(false)->after('lng_tujuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'lat_tujuan', 'lng_tujuan', 'pakai_pin']);
        });
    }
};