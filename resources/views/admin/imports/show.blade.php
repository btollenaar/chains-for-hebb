@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Import Details</h1>
                <p class="text-gray-600 mt-1">{{ ucfirst($import->type) }} import: {{ $import->original_filename }}</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                @if($import->failed_rows > 0)
                    <a href="{{ route('admin.imports.errors', $import) }}" class="btn-admin-secondary btn-admin-sm">
                        <i class="fas fa-download mr-2"></i>Download Errors
                    </a>
                @endif
                <a href="{{ route('admin.imports.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Imports
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12" x-data="{
        status: '{{ $import->status }}',
        progress: {{ $import->progress_percent }},
        processed: {{ $import->processed_rows }},
        total: {{ $import->total_rows }},
        successful: {{ $import->successful_rows }},
        failed: {{ $import->failed_rows }},
        intervalId: null,
        init() {
            if (this.status === 'pending' || this.status === 'processing') {
                this.poll();
            }
        },
        poll() {
            this.intervalId = setInterval(async () => {
                try {
                    const res = await fetch('{{ route("admin.imports.progress", $import) }}');
                    const data = await res.json();
                    this.status = data.status;
                    this.progress = data.progress_percent;
                    this.processed = data.processed_rows;
                    this.total = data.total_rows;
                    this.successful = data.successful_rows;
                    this.failed = data.failed_rows;
                    if (data.status === 'completed' || data.status === 'failed') {
                        clearInterval(this.intervalId);
                        location.reload();
                    }
                } catch (e) {
                    // Silently handle polling errors
                }
            }, 3000);
        }
    }">
        <div class="max-w-4xl mx-auto">
            <!-- Import Info Card -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Type</p>
                        <p class="text-sm font-medium text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $import->type === 'products' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                                {{ ucfirst($import->type) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Filename</p>
                        <p class="text-sm font-medium text-gray-900">{{ $import->original_filename }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Uploaded By</p>
                        <p class="text-sm font-medium text-gray-900">{{ $import->uploader->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Upload Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $import->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if($import->started_at)
                        <div>
                            <p class="text-xs text-gray-500">Started</p>
                            <p class="text-sm font-medium text-gray-900">{{ $import->started_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($import->completed_at)
                        <div>
                            <p class="text-xs text-gray-500">Completed</p>
                            <p class="text-sm font-medium text-gray-900">{{ $import->completed_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status & Progress -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Progress</h2>

                <!-- Status Badge -->
                <div class="mb-4">
                    <template x-if="status === 'pending'">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-2"></i> Pending
                        </span>
                    </template>
                    <template x-if="status === 'processing'">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Processing
                        </span>
                    </template>
                    <template x-if="status === 'completed'">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i> Completed
                        </span>
                    </template>
                    <template x-if="status === 'failed'">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-circle mr-2"></i> Failed
                        </span>
                    </template>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span x-text="processed + ' / ' + total + ' rows processed'"></span>
                        <span x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500"
                             :class="status === 'failed' ? 'bg-red-500' : (status === 'completed' ? 'bg-green-500' : 'bg-blue-500')"
                             :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900" x-text="total">{{ $import->total_rows }}</p>
                        <p class="text-xs text-gray-500">Total Rows</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600" x-text="processed">{{ $import->processed_rows }}</p>
                        <p class="text-xs text-gray-500">Processed</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600" x-text="successful">{{ $import->successful_rows }}</p>
                        <p class="text-xs text-gray-500">Successful</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-red-600" x-text="failed">{{ $import->failed_rows }}</p>
                        <p class="text-xs text-gray-500">Failed</p>
                    </div>
                </div>
            </div>

            <!-- Error Log -->
            @if(!empty($import->error_log) && count($import->error_log) > 0)
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            Error Log ({{ count($import->error_log) }} errors)
                        </h2>
                        <a href="{{ route('admin.imports.errors', $import) }}" class="btn-admin-secondary btn-admin-sm">
                            <i class="fas fa-download mr-1"></i> CSV
                        </a>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Row</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error Message</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($import->error_log as $error)
                                    <tr>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $error['row'] }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-red-600">
                                            {{ $error['message'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden p-4 space-y-3">
                        @foreach($import->error_log as $error)
                            <div class="bg-red-50 border border-red-100 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700 mb-1">Row {{ $error['row'] }}</p>
                                <p class="text-sm text-red-600">{{ $error['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
