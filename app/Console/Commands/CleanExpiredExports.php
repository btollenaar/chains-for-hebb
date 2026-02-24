<?php

namespace App\Console\Commands;

use App\Models\DataExport;
use Illuminate\Console\Command;

class CleanExpiredExports extends Command
{
    protected $signature = 'exports:clean-expired';
    protected $description = 'Delete expired data export files and records';

    public function handle(): int
    {
        $expired = DataExport::where('expires_at', '<', now())
            ->where('status', 'completed')
            ->get();

        $deleted = 0;
        foreach ($expired as $export) {
            if ($export->file_path) {
                $fullPath = storage_path('app/' . $export->file_path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $export->delete();
            $deleted++;
        }

        $this->info("Cleaned up {$deleted} expired data exports.");
        return self::SUCCESS;
    }
}
