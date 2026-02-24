@extends('layouts.admin')

@section('content')
    <div class="pb-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit About Page</h1>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $about->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal px-3 py-2 border"
                                placeholder="e.g., Business Owner">
                        </div>

                        <!-- Credentials -->
                        <div>
                            <label for="credentials" class="block text-sm font-medium text-gray-700">Credentials</label>
                            <textarea name="credentials" id="credentials" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal px-3 py-2 border tinymce"
                                placeholder="e.g., DNAP, CRNA, APRN">{{ old('credentials', $about->credentials) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Professional credentials and certifications</p>
                        </div>

                        <!-- Short Bio -->
                        <div>
                            <label for="short_bio" class="block text-sm font-medium text-gray-700">Short Bio</label>
                            <textarea name="short_bio" id="short_bio" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal px-3 py-2 border tinymce"
                                placeholder="Brief introduction (max 500 characters)">{{ old('short_bio', $about->short_bio) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Brief introduction for the about page</p>
                        </div>

                        <!-- Full Bio -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Full Biography</label>
                            <textarea name="bio" id="bio" rows="10"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal px-3 py-2 border tinymce"
                                placeholder="Full biography and professional details">{{ old('bio', $about->bio) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Detailed biography for the about page</p>
                        </div>

                        <!-- Image -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                            <div class="mt-2 flex items-center space-x-4">
                                @if ($about->image)
                                    <div>
                                        <img src="{{ asset('storage/' . $about->image) }}" alt="{{ $about->name }}" class="h-32 w-32 object-cover rounded-lg">
                                        <p class="text-xs text-gray-500 mt-1">{{ $about->image }}</p>
                                    </div>
                                @endif
                                <input type="file" name="image" id="image" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-admin-teal hover:file:bg-teal-100">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF up to 2MB. Upload to replace current image.</p>
                        </div>

                        <!-- Published -->
                        <div class="flex items-center">
                            <input type="checkbox" name="published" id="published" value="1" {{ $about->published ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-admin-teal shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                            <label for="published" class="ml-2 block text-sm text-gray-700">Publish this page</label>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <a href="{{ route('admin.dashboard') }}" class="btn-admin-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-admin-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/kh3vhfgxdfo6kz7tzjfulah6hs735glyg7cr378gob5ljlg3/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        height: 300,
        menubar: false,
        plugins: 'lists link code help',
        toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link | code | help',
        valid_elements: 'p,br,strong/b,em/i,u,a[href|title|target],ul,ol,li,blockquote,code,pre',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
    });
</script>
@endpush
