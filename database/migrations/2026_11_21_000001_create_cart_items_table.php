<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('item_type', 20); // 'produk' | 'limbah'
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('qty')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'item_type', 'item_id'], 'cart_items_user_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
