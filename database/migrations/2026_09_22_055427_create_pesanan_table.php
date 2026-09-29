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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kode_pesanan')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('jarak_km', 8, 2)->default(0);
            $table->decimal('total_harga', 12, 2);

            // Payment info
            $table->enum('payment_method', ['COD', 'Transfer']);
            $table->string('delivery_type')->default('delivery'); // pickup / delivery
            $table->string('bank_tujuan')->nullable(); // BCA / BRI / Mandiri / BNI
            $table->string('va_number')->nullable();   // Virtual Account

            // Status
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('order_status', ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('pending');
            $table->string('alasan_batal')->nullable();

            // Pengiriman & waktu
            $table->text('alamat_pengiriman');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_order')->useCurrent();
            $table->timestamp('expired_at')->nullable(); // expired transfer 24 jam

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};