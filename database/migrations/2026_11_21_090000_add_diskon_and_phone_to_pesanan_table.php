<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'diskon')) {
                $table->decimal('diskon', 12, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('pesanan', 'no_telepon_penerima')) {
                $table->string('no_telepon_penerima', 30)->nullable()->index()->after('alamat_pengiriman');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan', 'no_telepon_penerima')) {
                $table->dropIndex(['no_telepon_penerima']);
                $table->dropColumn('no_telepon_penerima');
            }
            if (Schema::hasColumn('pesanan', 'diskon')) {
                $table->dropColumn('diskon');
            }
        });
    }
};
