@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sponsor Tiers</h1>
                <p class="text-gray-600 mt-1">Manage sponsorship tier levels and benefits</p>
            </div>
            <a href="{{ route('admin.sponsor-tiers.create') }}" class="btn-admin-primary">
                <i class="fas fa-plus mr-2"></i>Add Tier
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Min Amount</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Logo Size</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tiers as $tier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $tier->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $tier->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        ${{ number_format($tier->min_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 uppercase">
                                            {{ $tier->logo_size }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ $tier->sort_order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.sponsor-tiers.edit', $tier) }}" class="text-admin-teal hover:text-teal-800 mr-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sponsor-tiers.destroy', $tier) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this tier?');">
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
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-medal text-gray-300 text-4xl mb-3 block"></i>
                                        No sponsor tiers found. <a href="{{ route('admin.sponsor-tiers.create') }}" class="text-admin-teal hover:underline">Create your first tier</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
