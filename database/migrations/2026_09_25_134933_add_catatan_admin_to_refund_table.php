<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refund', function (Blueprint $table) {
            $table->text('catatan_admin')->nullable()->after('status_refund');
        });
    }

    public function down(): void
    {
        Schema::table('refund', function (Blueprint $table) {
            $table->dropColumn('catatan_admin');
        });
    }
};