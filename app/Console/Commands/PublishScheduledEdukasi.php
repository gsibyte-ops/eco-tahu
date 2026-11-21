<?php

namespace App\Console\Commands;

use App\Models\Edukasi;
use App\Models\Resep;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishScheduledEdukasi extends Command
{
    /**
     * Nama command yang dipanggil via artisan.
     */
    protected $signature = 'edukasi:publish-scheduled';

    /**
     * Deskripsi command.
     */
    protected $description = 'Publish artikel edukasi dan resep yang sudah waktunya tayang';

    /**
     * Eksekusi command.
     */
    public function handle(): int
    {
        $edukasiCount = 0;
        $resepCount = 0;

        DB::transaction(function () use (&$edukasiCount, &$resepCount) {
            $dueEdukasi = Edukasi::where('status', 'scheduled')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', now())
                ->lockForUpdate()
                ->get();

            foreach ($dueEdukasi as $item) {
                $item->update([
                    'status'       => 'publish',
                    'published_at' => now(),
                ]);
                $edukasiCount++;
            }

            $dueResep = Resep::where('status', 'scheduled')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', now())
                ->lockForUpdate()
                ->get();

            foreach ($dueResep as $item) {
                $item->update([
                    'status'       => 'publish',
                    'published_at' => now(),
                ]);
                $resepCount++;
            }
        });

        $total = $edukasiCount + $resepCount;
        if ($total > 0) {
            $this->info("{$edukasiCount} artikel edukasi dan {$resepCount} resep dipublish otomatis.");
        } else {
            $this->line('Tidak ada artikel atau resep yang perlu dipublish.');
        }

        return self::SUCCESS;
    }
}