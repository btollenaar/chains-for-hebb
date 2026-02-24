<?php

namespace App\Jobs;

use App\Mail\DataExportReadyMail;
use App\Models\DataExport;
use App\Services\DataExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateDataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 1;

    public function __construct(public DataExport $dataExport) {}

    public function handle(DataExportService $service): void
    {
        $this->dataExport->update(['status' => 'processing']);

        try {
            $filePath = $service->generateExport($this->dataExport->customer);

            $this->dataExport->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'completed_at' => now(),
                'expires_at' => now()->addDays(7),
            ]);

            try {
                Mail::to($this->dataExport->customer->email)
                    ->send(new DataExportReadyMail($this->dataExport));
            } catch (\Exception $e) {
                Log::error('Data export email failed', [
                    'export_id' => $this->dataExport->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            $this->dataExport->update(['status' => 'failed']);
            Log::error('Data export generation failed', [
                'export_id' => $this->dataExport->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
