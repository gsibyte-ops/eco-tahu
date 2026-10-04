<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');

            // Polymorphic: bisa ProdukTahu atau Limbah
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');

            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('komentar')->nullable();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id']);

            // Cegah spam: 1 pesanan cuma bisa review item yang sama SEKALI
            $table->unique(
                ['user_id', 'pesanan_id', 'reviewable_type', 'reviewable_id'],
                'unique_review_per_pesanan'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};