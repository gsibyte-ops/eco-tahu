<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'media_path')) {
                $table->string('media_path')->nullable()->after('komentar');
            }
            if (!Schema::hasColumn('reviews', 'media_type')) {
                $table->string('media_type', 20)->nullable()->after('media_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('reviews', 'media_path')) {
                $cols[] = 'media_path';
            }
            if (Schema::hasColumn('reviews', 'media_type')) {
                $cols[] = 'media_type';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
