@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
        <p class="text-sm text-gray-600 mt-1">Update event details</p>
    </div>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                   value="{{ old('title', $event->title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="md:col-span-2">
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                Slug (URL-friendly name)
                            </label>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', $event->slug) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Brief event description (max 1000 characters)</p>
                        </div>

                        <!-- Content (TinyMCE) -->
                        <div class="md:col-span-2">
                            <label for="content-editor" class="block text-sm font-medium text-gray-700 mb-2">
                                Content
                            </label>
                            <textarea name="content" id="content-editor" rows="8"
                                      class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('content', $event->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Event Type <span class="text-red-500">*</span>
                            </label>
                            <select name="event_type" id="event_type" required
                                    class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                                <option value="">Select a type...</option>
                                <option value="work_party" {{ old('event_type', $event->event_type) === 'work_party' ? 'selected' : '' }}>Work Party</option>
                                <option value="fundraiser" {{ old('event_type', $event->event_type) === 'fundraiser' ? 'selected' : '' }}>Fundraiser</option>
                                <option value="meetup" {{ old('event_type', $event->event_type) === 'meetup' ? 'selected' : '' }}>Meetup</option>
                                <option value="tournament" {{ old('event_type', $event->event_type) === 'tournament' ? 'selected' : '' }}>Tournament</option>
                            </select>
                            @error('event_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Name -->
                        <div>
                            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Location
                            </label>
                            <input type="text" name="location_name" id="location_name"
                                   value="{{ old('location_name', $event->location_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('location_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Schedule</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Starts At -->
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Starts At <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="starts_at" id="starts_at" required
                                   value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('starts_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ends At -->
                        <div>
                            <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Ends At
                            </label>
                            <input type="datetime-local" name="ends_at" id="ends_at"
                                   value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('ends_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- RSVP Deadline -->
                        <div>
                            <label for="rsvp_deadline" class="block text-sm font-medium text-gray-700 mb-2">
                                RSVP Deadline
                            </label>
                            <input type="datetime-local" name="rsvp_deadline" id="rsvp_deadline"
                                   value="{{ old('rsvp_deadline', $event->rsvp_deadline?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('rsvp_deadline')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Must be before the event start time</p>
                        </div>
                    </div>
                </div>

                <!-- Capacity & Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Capacity &amp; Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Max Attendees -->
                        <div>
                            <label for="max_attendees" class="block text-sm font-medium text-gray-700 mb-2">
                                Max Attendees
                            </label>
                            <input type="number" name="max_attendees" id="max_attendees" min="1"
                                   value="{{ old('max_attendees', $event->max_attendees) }}"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('max_attendees')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Leave blank for unlimited capacity</p>
                        </div>

                        <!-- What to Bring -->
                        <div>
                            <label for="what_to_bring" class="block text-sm font-medium text-gray-700 mb-2">
                                What to Bring
                            </label>
                            <textarea name="what_to_bring" id="what_to_bring" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('what_to_bring', $event->what_to_bring) }}</textarea>
                            @error('what_to_bring')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Image & Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Image &amp; Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Featured Image -->
                        <div class="md:col-span-2">
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Image
                            </label>
                            @if($event->featured_image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $event->featured_image) }}" alt="{{ $event->title }}"
                                         class="w-48 h-32 object-cover rounded-lg shadow-sm">
                                    <p class="mt-1 text-xs text-gray-500">Current image. Upload a new one to replace it.</p>
                                </div>
                            @endif
                            <input type="file" name="featured_image" id="featured_image" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Max 5MB. JPEG, PNG, GIF, or WebP.</p>
                        </div>

                        <!-- Is Published -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Publish</label>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_published" id="is_published" value="1"
                                       {{ old('is_published', $event->is_published) ? 'checked' : '' }}
                                       class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                                <label for="is_published" class="ml-2 text-sm text-gray-700">
                                    Publish this event on the website
                                </label>
                            </div>
                        </div>

                        <!-- Is Featured -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                       {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}
                                       class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                                <label for="is_featured" class="ml-2 text-sm text-gray-700">
                                    Feature this event prominently
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('admin.events.index') }}" class="btn-admin-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Events
                    </a>
                    <button type="submit" class="btn-admin-primary">
                        <i class="fas fa-save mr-2"></i>Update Event
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete Section -->
        <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6 mt-6">
            <h3 class="text-lg font-bold text-red-600 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-600 mb-4">Deleting this event will also remove all associated RSVPs. This action cannot be undone.</p>
            <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors">
                    <i class="fas fa-trash mr-2"></i>Delete Event
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        tinymce.init({
            selector: '#content-editor',
            height: 400,
            menubar: false,
            plugins: 'lists link image',
            toolbar: 'undo redo | bold italic | bullist numlist | link image'
        });
    </script>
    @endpush
@endsection
