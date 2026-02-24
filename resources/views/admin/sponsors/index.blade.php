@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sponsors</h1>
                <p class="text-gray-600 mt-1">Manage sponsors and sponsorship partnerships</p>
            </div>
            <a href="{{ route('admin.sponsors.create') }}" class="btn-admin-primary">
                <i class="fas fa-plus mr-2"></i>Add Sponsor
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($sponsors as $sponsor)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sponsor->logo)
                                            <img src="{{ asset('storage/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}"
                                                 class="h-10 w-10 rounded object-contain">
                                        @else
                                            <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-building text-gray-400"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $sponsor->name }}</div>
                                        @if($sponsor->website_url)
                                            <a href="{{ $sponsor->website_url }}" target="_blank" class="text-xs text-admin-teal hover:underline">
                                                {{ parse_url($sponsor->website_url, PHP_URL_HOST) }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sponsor->tier->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        ${{ number_format($sponsor->sponsorship_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($sponsor->is_active)
                                            <span class="inline-block h-3 w-3 rounded-full bg-green-500" title="Active"></span>
                                        @else
                                            <span class="inline-block h-3 w-3 rounded-full bg-red-500" title="Inactive"></span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($sponsor->is_featured)
                                            <i class="fas fa-star text-yellow-500"></i>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="text-admin-teal hover:text-teal-800 mr-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this sponsor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-handshake text-gray-300 text-4xl mb-3 block"></i>
                                        No sponsors found. <a href="{{ route('admin.sponsors.create') }}" class="text-admin-teal hover:underline">Add your first sponsor</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($sponsors->hasPages())
                <div class="mt-6">
                    {{ $sponsors->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
