<?php

namespace App\Jobs;

use App\Models\CsvImport;
use App\Services\CsvImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries = 1;

    public function __construct(public CsvImport $import) {}

    public function handle(CsvImportService $service): void
    {
        $this->import->markAsProcessing();

        try {
            match ($this->import->type) {
                'products' => $service->importProducts($this->import),
                'customers' => $service->importCustomers($this->import),
            };
        } catch (\Exception $e) {
            Log::error('CSV import failed', [
                'import_id' => $this->import->id,
                'error' => $e->getMessage(),
            ]);
            $this->import->markAsFailed();
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->import->markAsFailed();
    }
}
