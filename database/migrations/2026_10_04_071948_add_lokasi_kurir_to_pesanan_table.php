<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanan', 'lat_kurir')) {
                $table->decimal('lat_kurir', 10, 8)->nullable()->after('lng_tujuan');
            }
            if (! Schema::hasColumn('pesanan', 'lng_kurir')) {
                $table->decimal('lng_kurir', 11, 8)->nullable()->after('lat_kurir');
            }
            if (! Schema::hasColumn('pesanan', 'lokasi_updated_at')) {
                $table->timestamp('lokasi_updated_at')->nullable()->after('lng_kurir');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['lat_kurir', 'lng_kurir', 'lokasi_updated_at']);
        });
    }
};