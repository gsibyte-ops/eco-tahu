<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('resep')) {
            Schema::create('resep', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('judul', 200)->unique();
                $table->string('slug')->unique();
                $table->text('deskripsi')->nullable();
                $table->longText('bahan');
                $table->longText('langkah');
                $table->unsignedInteger('durasi_menit')->default(15);
                $table->unsignedInteger('porsi')->default(2);
                $table->string('tingkat_kesulitan', 30)->default('mudah');
                $table->string('media_type', 20)->default('image');
                $table->string('thumbnail')->nullable();
                $table->string('video_path')->nullable();
                $table->string('youtube_url')->nullable();
                $table->string('status', 20)->default('draft');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('resep_likes')) {
            Schema::create('resep_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('resep_id')->constrained('resep')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['resep_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_likes');
        Schema::dropIfExists('resep');
    }
};
