@props([
    'name',
    'label',
    'currentPath' => null,
    'description' => null,
    'required' => false,
    'accept' => 'image/jpeg,image/png,image/jpg,image/gif,image/webp',
    'maxSize' => 5120, // KB
    'previewWidth' => 'w-32',
    'previewHeight' => 'h-32',
    'showBackground' => false,
    'objectFit' => 'object-cover'
])

<div x-data="{
    imagePreview: '{{ $currentPath ?? '' }}',
    fileName: '{{ $currentPath ? basename($currentPath) : '' }}',

    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },

    clearImage() {
        this.imagePreview = '';
        this.fileName = '';
        $refs.fileInput.value = '';
    }
}" class="mb-6">

    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Current Image Preview -->
    <div x-show="imagePreview" class="mb-3">
        <div class="flex items-start gap-4">
            <img :src="imagePreview"
                 class="{{ $previewWidth }} {{ $previewHeight }} {{ $objectFit }} rounded-lg border-2 border-gray-200 {{ $showBackground ? 'bg-gray-300 p-2' : '' }}"
                 alt="Preview">
            <div class="flex-1">
                <p class="text-sm text-gray-600" x-text="fileName"></p>
                <button type="button"
                        @click="clearImage()"
                        class="mt-2 text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-trash-alt mr-1"></i> Remove
                </button>
            </div>
        </div>
    </div>

    <!-- Upload Input -->
    <input type="file"
           x-ref="fileInput"
           name="{{ $name }}"
           id="{{ $name }}"
           @change="previewImage($event)"
           accept="{{ $accept }}"
           class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-md file:border-0
                  file:text-sm file:font-semibold
                  file:text-white
                  hover:file:bg-opacity-90
                  cursor-pointer"
           style="file:background-color: #2D6069;"
           {{ $required && !$currentPath ? 'required' : '' }}>

    @if($description)
        <p class="text-sm text-gray-500 mt-2">
            <i class="fas fa-info-circle mr-1"></i> {{ $description }}
        </p>
    @endif

    <p class="text-xs text-gray-400 mt-1">
        Accepted: JPG, PNG, GIF, WebP. Max size: {{ number_format($maxSize / 1024, 1) }}MB
    </p>

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
