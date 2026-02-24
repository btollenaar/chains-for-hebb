<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDataExport;
use App\Models\DataExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataExportController extends Controller
{
    public function request(Request $request)
    {
        // Throttle: only 1 export per 24 hours
        $recentExport = DataExport::where('customer_id', Auth::id())
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($recentExport) {
            return redirect()->route('dashboard')
                ->with('error', 'You can only request one data export per 24 hours. Please try again later.');
        }

        $export = DataExport::create([
            'customer_id' => Auth::id(),
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        GenerateDataExport::dispatch($export);

        return redirect()->route('dashboard')
            ->with('success', 'Your data export has been requested. You will receive an email when it is ready to download.');
    }

    public function download(DataExport $export, Request $request)
    {
        // Simple signature verification
        $expectedSignature = hash_hmac('sha256', $export->id, config('app.key'));
        if ($request->query('signature') !== $expectedSignature) {
            abort(403, 'Invalid download link.');
        }

        if ($export->is_expired) {
            return redirect()->route('dashboard')
                ->with('error', 'This download link has expired. Please request a new export.');
        }

        if ($export->status !== 'completed' || !$export->file_path) {
            return redirect()->route('dashboard')
                ->with('error', 'This export is not yet ready or has failed.');
        }

        $fullPath = storage_path('app/' . $export->file_path);
        if (!file_exists($fullPath)) {
            return redirect()->route('dashboard')
                ->with('error', 'Export file not found. Please request a new export.');
        }

        return response()->download($fullPath, 'my-data-export.zip');
    }
}
