@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">CSV Imports</h1>
                <p class="text-gray-600 mt-1">Import products and customers from CSV files</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.imports.create') }}" class="btn-admin-primary">
                    <i class="fas fa-upload mr-2"></i>New Import
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Imports</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats->total) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-file-import text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats->completed) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Processing</p>
                            <p class="text-2xl md:text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats->processing) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-spinner text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Failed</p>
                            <p class="text-2xl md:text-3xl font-bold text-red-600 mt-2">{{ number_format($stats->failed) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-exclamation-circle text-red-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Imports Table (Desktop) -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rows</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($imports as $import)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $import->created_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $import->type === 'products' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                                            {{ ucfirst($import->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ Str::limit($import->original_filename, 30) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($import->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        @elseif($import->status === 'processing')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>
                                        @elseif($import->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                        @elseif($import->status === 'failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="text-green-600">{{ $import->successful_rows }}</span> /
                                        <span class="text-red-600">{{ $import->failed_rows }}</span> /
                                        {{ $import->total_rows }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $import->status === 'failed' ? 'bg-red-500' : ($import->status === 'completed' ? 'bg-green-500' : 'bg-blue-500') }}"
                                                 style="width: {{ $import->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $import->progress_percent }}%</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.imports.show', $import) }}" class="text-admin-teal hover:underline">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-file-import text-4xl mb-3 text-gray-300"></i>
                                        <p>No imports yet. Upload a CSV file to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="grid grid-cols-1 gap-4 md:hidden p-4">
                    @forelse($imports as $import)
                        <a href="{{ route('admin.imports.show', $import) }}" class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $import->type === 'products' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                                    {{ ucfirst($import->type) }}
                                </span>
                                @if($import->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($import->status === 'processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>
                                @elseif($import->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                @elseif($import->status === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                @endif
                            </div>
                            <p class="text-sm font-medium text-gray-900 mb-1">{{ Str::limit($import->original_filename, 40) }}</p>
                            <p class="text-xs text-gray-500 mb-2">{{ $import->created_at->format('M j, Y g:i A') }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    <span class="text-green-600">{{ $import->successful_rows }}</span> /
                                    <span class="text-red-600">{{ $import->failed_rows }}</span> /
                                    {{ $import->total_rows }} rows
                                </span>
                                <span class="text-xs text-gray-500">{{ $import->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="h-1.5 rounded-full {{ $import->status === 'failed' ? 'bg-red-500' : ($import->status === 'completed' ? 'bg-green-500' : 'bg-blue-500') }}"
                                     style="width: {{ $import->progress_percent }}%"></div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-file-import text-4xl mb-3 text-gray-300"></i>
                            <p>No imports yet. Upload a CSV file to get started.</p>
                        </div>
                    @endforelse
                </div>

                @if($imports->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $imports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
