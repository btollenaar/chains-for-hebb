@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Fundraising Dashboard</h1>
        <p class="text-gray-600 mt-1">Track fundraising progress, milestones, and budget allocation</p>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">

            <!-- Progress Overview Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Progress Overview</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Raised</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">${{ number_format($progressData['total_raised'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Goal Amount</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($progressData['goal_amount'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Percentage</p>
                        <p class="text-3xl font-bold text-admin-teal mt-1">{{ number_format($progressData['percentage'] ?? 0, 1) }}%</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-500"
                         style="width: {{ min($progressData['percentage'] ?? 0, 100) }}%"></div>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    ${{ number_format($progressData['total_raised'] ?? 0, 2) }} of ${{ number_format($progressData['goal_amount'] ?? 0, 2) }} raised
                </p>
            </div>

            <!-- Revenue Breakdown Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Revenue Breakdown</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-800">Donations Total</p>
                                <p class="text-2xl font-bold text-green-700 mt-1">${{ number_format($progressData['donations_total'] ?? 0, 2) }}</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <i class="fas fa-heart text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-800">Sponsor Total</p>
                                <p class="text-2xl font-bold text-blue-700 mt-1">${{ number_format($progressData['sponsors_total'] ?? 0, 2) }}</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <i class="fas fa-handshake text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-800">Merch Profit</p>
                                <p class="text-2xl font-bold text-purple-700 mt-1">${{ number_format($progressData['merch_profit'] ?? 0, 2) }}</p>
                            </div>
                            <div class="bg-purple-100 rounded-full p-3">
                                <i class="fas fa-tshirt text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two-column section: Milestones & Budget Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Milestones -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Milestones</h2>
                        <a href="{{ route('admin.fundraising.milestones.create') }}" class="btn-admin-primary btn-admin-sm">
                            <i class="fas fa-plus mr-1"></i>Add Milestone
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Reached</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Reached At</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($milestones as $milestone)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @if($milestone->icon)
                                                <i class="{{ $milestone->icon }} mr-1 text-gray-400"></i>
                                            @endif
                                            {{ $milestone->title }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900">
                                            ${{ number_format($milestone->target_amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            @if($milestone->is_reached)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Yes
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                            {{ $milestone->reached_at ? $milestone->reached_at->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('admin.fundraising.milestones.edit', $milestone) }}" class="text-admin-teal hover:text-teal-800 mr-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.fundraising.milestones.destroy', $milestone) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this milestone?');">
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
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-flag text-gray-300 text-3xl mb-2 block"></i>
                                            No milestones yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Budget Breakdown -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Budget Breakdown</h2>
                        <a href="{{ route('admin.fundraising.breakdowns.create') }}" class="btn-admin-primary btn-admin-sm">
                            <i class="fas fa-plus mr-1"></i>Add Item
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sort</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($breakdowns as $breakdown)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $breakdown->label }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900">
                                            ${{ number_format($breakdown->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="inline-block h-5 w-5 rounded border border-gray-300"
                                                  style="background-color: {{ $breakdown->color ?? '#6B7280' }};"
                                                  title="{{ $breakdown->color }}"></span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $breakdown->sort_order }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('admin.fundraising.breakdowns.edit', $breakdown) }}" class="text-admin-teal hover:text-teal-800 mr-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.fundraising.breakdowns.destroy', $breakdown) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this budget item?');">
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
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-chart-pie text-gray-300 text-3xl mb-2 block"></i>
                                            No budget items yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
