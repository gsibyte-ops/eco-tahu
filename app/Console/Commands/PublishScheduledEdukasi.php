<?php

namespace App\Console\Commands;

use App\Models\Edukasi;
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
    protected $description = 'Publish artikel edukasi yang sudah waktunya tayang';

    /**
     * Eksekusi command.
     */
    public function handle(): int
    {
        $count = 0;

        DB::transaction(function () use (&$count) {
            $due = Edukasi::where('status', 'scheduled')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', now())
                ->lockForUpdate()
                ->get();

            foreach ($due as $item) {
                $item->update([
                    'status'       => 'publish',
                    'published_at' => now(),
                ]);
                $count++;
            }
        });

        if ($count > 0) {
            $this->info("✅ {$count} artikel dipublish otomatis.");
        } else {
            $this->line('Tidak ada artikel yang perlu dipublish.');
        }

        return self::SUCCESS;
    }
}