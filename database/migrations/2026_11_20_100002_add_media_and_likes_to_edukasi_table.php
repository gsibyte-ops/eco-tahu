<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edukasi', function (Blueprint $table) {
            if (!Schema::hasColumn('edukasi', 'media_type')) {
                $table->string('media_type', 20)->default('image')->after('thumbnail');
            }
            if (!Schema::hasColumn('edukasi', 'video_path')) {
                $table->string('video_path')->nullable()->after('media_type');
            }
            if (!Schema::hasColumn('edukasi', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('video_path');
            }
        });

        if (!Schema::hasTable('edukasi_likes')) {
            Schema::create('edukasi_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('edukasi_id')->constrained('edukasi')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['edukasi_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('edukasi_likes');

        Schema::table('edukasi', function (Blueprint $table) {
            $cols = [];
            foreach (['media_type', 'video_path', 'youtube_url'] as $col) {
                if (Schema::hasColumn('edukasi', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
