@extends('layouts.admin')

@section('title', 'Audit Log Detail')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Audit Log Detail</h1>
                <p class="text-gray-600 mt-1">Entry #{{ $auditLog->id }} - {{ ucfirst($auditLog->action) }} {{ $auditLog->model_type }}</p>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="btn-admin-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Audit Log
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content: Changes Diff -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Action Summary -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                @switch($auditLog->action)
                                    @case('created')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-plus-circle mr-2"></i> Created
                                        </span>
                                        @break
                                    @case('updated')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-edit mr-2"></i> Updated
                                        </span>
                                        @break
                                    @case('deleted')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-trash mr-2"></i> Deleted
                                        </span>
                                        @break
                                    @case('exported')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <i class="fas fa-download mr-2"></i> Exported
                                        </span>
                                        @break
                                    @case('imported')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">
                                            <i class="fas fa-upload mr-2"></i> Imported
                                        </span>
                                        @break
                                    @default
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($auditLog->action) }}
                                        </span>
                                @endswitch
                                <span class="ml-3 text-lg font-bold text-gray-900">
                                    {{ $auditLog->model_type }}@if($auditLog->model_id) #{{ $auditLog->model_id }}@endif
                                </span>
                            </div>
                            @if($auditLog->model_label)
                                <p class="text-gray-600">
                                    <i class="fas fa-tag mr-2 text-gray-400"></i>{{ $auditLog->model_label }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Changes Table -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">
                                <i class="fas fa-exchange-alt mr-2 text-admin-teal"></i>Changes
                            </h3>

                            @if($auditLog->action === 'created' && $auditLog->new_values)
                                <p class="text-sm text-gray-500 mb-4">New values when the record was created:</p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($auditLog->new_values as $field => $value)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $field }}</td>
                                                    <td class="px-4 py-3 text-sm text-green-700 bg-green-50 break-all">
                                                        @if(is_array($value) || is_object($value))
                                                            <code class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code>
                                                        @elseif(is_null($value))
                                                            <span class="text-gray-400 italic">null</span>
                                                        @elseif(is_bool($value))
                                                            <span>{{ $value ? 'true' : 'false' }}</span>
                                                        @else
                                                            {{ Str::limit((string) $value, 500) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($auditLog->action === 'deleted' && $auditLog->old_values)
                                <p class="text-sm text-gray-500 mb-4">Values at the time of deletion:</p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($auditLog->old_values as $field => $value)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $field }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-700 bg-red-50 break-all">
                                                        @if(is_array($value) || is_object($value))
                                                            <code class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code>
                                                        @elseif(is_null($value))
                                                            <span class="text-gray-400 italic">null</span>
                                                        @elseif(is_bool($value))
                                                            <span>{{ $value ? 'true' : 'false' }}</span>
                                                        @else
                                                            {{ Str::limit((string) $value, 500) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($auditLog->action === 'updated' && $auditLog->old_values && $auditLog->new_values)
                                <p class="text-sm text-gray-500 mb-4">Fields that changed:</p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($auditLog->new_values as $field => $newValue)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $field }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-700 bg-red-50 break-all">
                                                        @php $oldValue = $auditLog->old_values[$field] ?? null; @endphp
                                                        @if(is_array($oldValue) || is_object($oldValue))
                                                            <code class="text-xs">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code>
                                                        @elseif(is_null($oldValue))
                                                            <span class="text-gray-400 italic">null</span>
                                                        @elseif(is_bool($oldValue))
                                                            <span>{{ $oldValue ? 'true' : 'false' }}</span>
                                                        @else
                                                            {{ Str::limit((string) $oldValue, 500) }}
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-green-700 bg-green-50 break-all">
                                                        @if(is_array($newValue) || is_object($newValue))
                                                            <code class="text-xs">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code>
                                                        @elseif(is_null($newValue))
                                                            <span class="text-gray-400 italic">null</span>
                                                        @elseif(is_bool($newValue))
                                                            <span>{{ $newValue ? 'true' : 'false' }}</span>
                                                        @else
                                                            {{ Str::limit((string) $newValue, 500) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <i class="fas fa-info-circle text-3xl mb-2 block"></i>
                                    <p>No detailed changes recorded for this entry.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- User Info -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">User Info</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">User</span>
                                    @if($auditLog->user)
                                        <span class="font-semibold text-gray-900">{{ $auditLog->user->name }}</span>
                                    @else
                                        <span class="text-gray-400 italic">System</span>
                                    @endif
                                </div>
                                @if($auditLog->user)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Email</span>
                                        <span class="text-sm text-gray-900">{{ $auditLog->user->email }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Role</span>
                                        <span class="text-sm text-gray-900 capitalize">{{ $auditLog->user->role }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Timestamp & Request Info -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Request Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Entry ID</span>
                                    <span class="font-semibold text-gray-900">#{{ $auditLog->id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Timestamp</span>
                                    <span class="font-semibold text-gray-900">{{ $auditLog->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Time</span>
                                    <span class="font-semibold text-gray-900">{{ $auditLog->created_at->format('g:i:s A') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Relative</span>
                                    <span class="text-sm text-gray-900">{{ $auditLog->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">IP Address</span>
                                    <span class="font-semibold text-gray-900">{{ $auditLog->ip_address ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Agent -->
                    @if($auditLog->user_agent)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">User Agent</h3>
                                <p class="text-xs text-gray-600 break-all leading-relaxed">{{ $auditLog->user_agent }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
