@extends('layouts.admin')

@section('title', $campaign->subject)

@section('content')
<div class="py-6" x-data="{ showTestModal: false, testEmails: '', sending: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.newsletters.campaigns.index') }}" class="text-admin-teal hover:text-admin-teal/80">
                <i class="fas fa-arrow-left mr-2"></i> Back to Campaigns
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Campaign Header -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">{{ $campaign->subject }}</h1>
                            @if($campaign->preview_text)
                                <p class="text-gray-500 mt-1">{{ $campaign->preview_text }}</p>
                            @endif
                        </div>
                        @if($campaign->status === 'draft')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                        @elseif($campaign->status === 'scheduled')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                        @elseif($campaign->status === 'sending')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">Sending</span>
                        @elseif($campaign->status === 'sent')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Sent</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($campaign->status) }}</span>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="flex space-x-3">
                            @if($campaign->status === 'draft')
                                <a href="{{ route('admin.newsletters.campaigns.edit', $campaign) }}" class="btn-admin-primary">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                            @endif
                            <button @click="showTestModal = true" class="btn-admin-secondary">
                                <i class="fas fa-paper-plane mr-2"></i> Send Test
                            </button>
                            <form action="{{ route('admin.newsletters.campaigns.duplicate', $campaign) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-admin-secondary">
                                    <i class="fas fa-copy mr-2"></i> Duplicate
                                </button>
                            </form>
                            @if($campaign->status === 'scheduled')
                                <form action="{{ route('admin.newsletters.campaigns.cancel', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this scheduled campaign?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        <i class="fas fa-times mr-2"></i> Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics (Sent Only): Mobile-optimized (2 cols mobile, 2 cols tablet, 4 cols desktop) -->
                @if($campaign->status === 'sent')
                    <div class="bg-white rounded-lg shadow p-4 md:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance</h2>
                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                            <div class="text-center">
                                <div class="text-2xl md:text-3xl font-bold text-gray-900">{{ number_format($campaign->sent_count) }}</div>
                                <div class="text-xs md:text-sm text-gray-500">Sent</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl md:text-3xl font-bold text-red-600">{{ number_format($campaign->failed_count) }}</div>
                                <div class="text-xs md:text-sm text-gray-500">Failed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl md:text-3xl font-bold text-green-600">{{ number_format($campaign->open_rate, 1) }}%</div>
                                <div class="text-xs md:text-sm text-gray-500">Open Rate</div>
                                <div class="text-xs text-gray-400">{{ number_format($campaign->open_count) }} opens</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl md:text-3xl font-bold text-blue-600">{{ number_format($campaign->click_rate, 1) }}%</div>
                                <div class="text-xs md:text-sm text-gray-500">Click Rate</div>
                                <div class="text-xs text-gray-400">{{ number_format($campaign->click_count) }} clicks</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Content Preview -->
                <div class="bg-white rounded-lg shadow" x-data="{ previewWidth: 700 }">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Content Preview</h2>
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button @click="previewWidth = 700"
                                class="px-3 py-1 text-sm font-medium rounded-md transition-colors"
                                :class="previewWidth === 700 ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'">
                                <i class="fas fa-desktop mr-1"></i>Desktop
                            </button>
                            <button @click="previewWidth = 375"
                                class="px-3 py-1 text-sm font-medium rounded-md transition-colors"
                                :class="previewWidth === 375 ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'">
                                <i class="fas fa-mobile-alt mr-1"></i>Mobile
                            </button>
                        </div>
                    </div>
                    <div class="p-6 flex justify-center">
                        <iframe src="{{ route('admin.newsletters.campaigns.preview', $campaign) }}"
                            class="border border-gray-200 rounded transition-all duration-300"
                            :style="'width:' + previewWidth + 'px; height: 600px;'"
                            sandbox="allow-same-origin"></iframe>
                    </div>
                </div>

                <!-- Recent Sends (Sent Only) -->
                @if($campaign->status === 'sent' && $recentSends->count() > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Sends (Last 100)</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Opened</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Clicked</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentSends as $send)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $send->subscription->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($send->status === 'sent')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Sent</span>
                                                @elseif($send->status === 'failed')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($send->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $send->sent_at ? $send->sent_at->format('M j, Y g:i A') : '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($send->opened_at)
                                                    <i class="fas fa-check-circle text-green-600"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-gray-300"></i>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($send->clicked_at)
                                                    <i class="fas fa-check-circle text-blue-600"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-gray-300"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Campaign Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Campaign Info</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ ucfirst($campaign->status) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $campaign->creator->name ?? 'Unknown' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $campaign->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        @if($campaign->scheduled_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Scheduled For</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $campaign->scheduled_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        @endif
                        @if($campaign->sent_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sent At</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $campaign->sent_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">From</dt>
                            <dd class="text-sm text-gray-900 mt-1">
                                {{ $campaign->from_name ?? config('mail.from.name') }}<br>
                                <span class="text-gray-500">{{ $campaign->from_email ?? config('mail.from.address') }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Subscriber Lists -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sent To</h3>
                    <ul class="space-y-2">
                        @foreach($campaign->lists as $list)
                            <li class="flex justify-between items-center">
                                <span class="text-sm text-gray-900">{{ $list->name }}</span>
                                <span class="text-sm text-gray-500">{{ number_format($list->subscriber_count) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center font-semibold">
                            <span class="text-sm text-gray-900">Total Recipients</span>
                            <span class="text-sm text-gray-900">{{ number_format($campaign->recipient_count) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                @if(in_array($campaign->status, ['draft', 'cancelled']))
                    <div class="bg-white rounded-lg shadow p-6 border-2 border-red-200">
                        <h3 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h3>
                        <form action="{{ route('admin.newsletters.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Delete Campaign
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Send Test Modal -->
    <div x-show="showTestModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showTestModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showTestModal = false"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Send Test Email</h3>
                    <button @click="showTestModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <label for="test_emails" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Addresses (comma-separated)
                    </label>
                    <textarea
                        id="test_emails"
                        x-model="testEmails"
                        rows="3"
                        placeholder="email1@example.com, email2@example.com"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50"
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500">Enter one or more email addresses separated by commas</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button @click="showTestModal = false" class="btn-admin-secondary">
                        Cancel
                    </button>
                    <button
                        @click="sendTest"
                        :disabled="sending || !testEmails.trim()"
                        class="btn-admin-primary"
                        :class="{ 'opacity-50 cursor-not-allowed': sending || !testEmails.trim() }"
                    >
                        <span x-show="!sending">Send Test</span>
                        <span x-show="sending">Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function sendTest() {
        this.sending = true;

        fetch('{{ route('admin.newsletters.campaigns.send-test', $campaign) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                test_emails: this.testEmails
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.notify(data.message, 'success');
                this.showTestModal = false;
                this.testEmails = '';
            } else {
                window.notify(data.message, 'error');
            }
        })
        .catch(error => {
            window.notify('Failed to send test email', 'error');
        })
        .finally(() => {
            this.sending = false;
        });
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
