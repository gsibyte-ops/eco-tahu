<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refund', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');
            $table->decimal('nominal_refund', 12, 2);
            $table->text('alasan_batal');
            $table->string('bukti_transfer_balik')->nullable();
            $table->enum('status_refund', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->timestamp('tanggal_refund')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund');
    }
};
