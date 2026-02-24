@extends('layouts.admin')

@section('title', 'Edit Newsletter Campaign')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.newsletters.campaigns.show', $campaign) }}" class="text-admin-teal hover:text-admin-teal/80">
                <i class="fas fa-arrow-left mr-2"></i> Back to Campaign
            </a>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Edit Newsletter Campaign</h2>
            </div>

            <form method="POST" action="{{ route('admin.newsletters.campaigns.update', $campaign) }}" x-data="{
                action: 'save_draft',
                showSchedule: false,
                selectedLists: @js($selectedLists),
                recipientCount: {{ $campaign->recipient_count }},
                calculateRecipients() {
                    this.recipientCount = this.selectedLists.length > 0 ? 'Calculating...' : 0;
                }
            }">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject', $campaign->subject) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview Text -->
                    <div>
                        <label for="preview_text" class="block text-sm font-medium text-gray-700 mb-2">
                            Preview Text
                        </label>
                        <input type="text" name="preview_text" id="preview_text" value="{{ old('preview_text', $campaign->preview_text) }}"
                            placeholder="Brief description shown in email clients"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                        @error('preview_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Newsletter Content <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" id="content" rows="15" class="wysiwyg-editor w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">{{ old('content', $campaign->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- From Name & Email (Optional Overrides) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">
                                From Name (Optional)
                            </label>
                            <input type="text" name="from_name" id="from_name" value="{{ old('from_name', $campaign->from_name) }}"
                                placeholder="Default: {{ config('mail.from.name') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                            @error('from_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">
                                From Email (Optional)
                            </label>
                            <input type="email" name="from_email" id="from_email" value="{{ old('from_email', $campaign->from_email) }}"
                                placeholder="Default: {{ config('mail.from.address') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                            @error('from_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Subscriber Lists -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Send To Lists <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2 border border-gray-300 rounded-md p-4 max-h-60 overflow-y-auto">
                            @foreach($lists as $list)
                                <label class="flex items-start">
                                    <input type="checkbox" name="lists[]" value="{{ $list->id }}"
                                        {{ (is_array(old('lists', $selectedLists)) && in_array($list->id, old('lists', $selectedLists))) ? 'checked' : '' }}
                                        x-model="selectedLists"
                                        @change="calculateRecipients()"
                                        class="mt-1 rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                                    <span class="ml-2 flex-1">
                                        <span class="font-medium text-gray-900">{{ $list->name }}</span>
                                        <span class="text-sm text-gray-500">({{ number_format($list->subscriber_count) }} subscribers)</span>
                                        @if($list->description)
                                            <p class="text-sm text-gray-500">{{ $list->description }}</p>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('lists')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Schedule DateTime (Conditional) -->
                    <div x-show="showSchedule" x-cloak>
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Schedule For <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                            value="{{ old('scheduled_at', $campaign->scheduled_at?->format('Y-m-d\TH:i')) }}"
                            min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                        @error('scheduled_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="action" x-model="action">

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('admin.newsletters.campaigns.show', $campaign) }}" class="btn-admin-secondary">
                        Cancel
                    </a>
                    <div class="flex space-x-3">
                        <button type="submit" @click="action = 'save_draft'; showSchedule = false" class="btn-admin-secondary">
                            Save as Draft
                        </button>
                        <button type="submit" @click="action = 'schedule'; showSchedule = true"
                            x-show="!showSchedule"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Schedule
                        </button>
                        <button type="submit" @click="action = 'send_now'; showSchedule = false" class="btn-admin-success">
                            Send Now
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<x-tinymce-init />

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
