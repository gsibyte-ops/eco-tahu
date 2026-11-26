<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'estimasi_menit')) {
                $table->unsignedSmallInteger('estimasi_menit')->nullable()->after('order_status');
            }
            if (!Schema::hasColumn('pesanan', 'estimasi_tiba')) {
                $table->timestamp('estimasi_tiba')->nullable()->after('estimasi_menit');
            }
            if (!Schema::hasColumn('pesanan', 'status_kurir')) {
                $table->string('status_kurir', 60)->nullable()->after('estimasi_tiba');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            foreach (['status_kurir', 'estimasi_tiba', 'estimasi_menit'] as $col) {
                if (Schema::hasColumn('pesanan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
