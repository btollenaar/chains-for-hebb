<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCsvImport;
use App\Models\CsvImport;
use App\Services\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvImportController extends Controller
{
    public function index()
    {
        $stats = CsvImport::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'processing' OR status = 'pending' THEN 1 ELSE 0 END) as processing,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
        ")->first();

        $imports = CsvImport::with('uploader')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.imports.index', compact('imports', 'stats'));
    }

    public function create()
    {
        return view('admin.imports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'type' => 'required|string|in:products,customers',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $storedPath = $file->store('imports');

        // Count data rows (exclude header)
        $totalRows = 0;
        $handle = fopen(Storage::path($storedPath), 'r');
        if ($handle !== false) {
            fgetcsv($handle); // Skip header
            while (fgetcsv($handle) !== false) {
                $totalRows++;
            }
            fclose($handle);
        }

        $import = CsvImport::create([
            'type' => $validated['type'],
            'filename' => $storedPath,
            'original_filename' => $originalName,
            'total_rows' => $totalRows,
            'uploaded_by' => Auth::id(),
        ]);

        ProcessCsvImport::dispatch($import);

        return redirect()
            ->route('admin.imports.show', $import)
            ->with('success', 'CSV file uploaded. Import is being processed.');
    }

    public function show(CsvImport $import)
    {
        $import->load('uploader');

        return view('admin.imports.show', compact('import'));
    }

    public function progress(CsvImport $import)
    {
        return response()->json([
            'processed_rows' => $import->processed_rows,
            'total_rows' => $import->total_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows,
            'status' => $import->status,
            'progress_percent' => $import->progress_percent,
        ]);
    }

    public function downloadTemplate(string $type)
    {
        if (!in_array($type, ['products', 'customers'])) {
            abort(404);
        }

        $service = new CsvImportService();
        $headers = $service->generateTemplate($type);

        $callback = function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fclose($handle);
        };

        $filename = "{$type}-import-template.csv";

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function downloadErrors(CsvImport $import)
    {
        if (empty($import->error_log)) {
            return redirect()
                ->route('admin.imports.show', $import)
                ->with('error', 'No errors to download.');
        }

        $callback = function () use ($import) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Row', 'Error Message']);
            foreach ($import->error_log as $error) {
                fputcsv($handle, [$error['row'], $error['message']]);
            }
            fclose($handle);
        };

        $filename = "import-{$import->id}-errors.csv";

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
