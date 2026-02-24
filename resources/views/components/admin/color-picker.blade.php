@props([
    'name',
    'label',
    'currentValue' => '#000000',
    'description' => null,
    'required' => false,
])

<div x-data="{
    color: '{{ $currentValue }}',

    updateFromPicker(event) {
        this.color = event.target.value;
    },

    updateFromText(event) {
        const hex = event.target.value;
        // Validate hex format
        if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
            this.color = hex;
        }
    },

    resetToDefault() {
        this.color = '{{ $currentValue }}';
    }
}" class="mb-6">

    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="flex items-center gap-4">
        <!-- Color Preview Swatch -->
        <div class="relative">
            <div :style="{ backgroundColor: color }"
                 class="w-16 h-16 rounded-lg border-2 border-gray-300 shadow-sm cursor-pointer transition-transform hover:scale-105"
                 @click="$refs.colorPicker.click()"
                 :aria-label="'Current color: ' + color">
            </div>
        </div>

        <!-- HTML5 Color Picker (Hidden) -->
        <input type="color"
               x-ref="colorPicker"
               x-model="color"
               @input="updateFromPicker($event)"
               class="sr-only">

        <!-- Manual Hex Input -->
        <div class="flex-1 max-w-xs">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-mono text-sm">
                    #
                </span>
                <input type="text"
                       :value="color.substring(1)"
                       @input="updateFromText({ target: { value: '#' + $event.target.value }})"
                       name="{{ $name }}"
                       id="{{ $name }}"
                       maxlength="6"
                       pattern="[0-9A-Fa-f]{6}"
                       class="pl-8 pr-4 py-2 w-full font-mono text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-offset-0 shadow-sm uppercase"
                       style="focus:ring-color: #2D6069; focus:border-color: #2D6069;"
                       placeholder="2E2A25"
                       {{ $required ? 'required' : '' }}>
            </div>
            <p class="text-xs text-gray-500 mt-1">Enter 6-digit hex code (without #)</p>
        </div>

        <!-- Click to Open Picker Button -->
        <button type="button"
                @click="$refs.colorPicker.click()"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md border border-gray-300 transition-colors text-sm font-medium">
            <i class="fas fa-palette mr-2"></i>Choose Color
        </button>

        <!-- Reset Button -->
        <button type="button"
                @click="resetToDefault()"
                class="px-3 py-2 text-gray-500 hover:text-gray-700 transition-colors"
                title="Reset to original value">
            <i class="fas fa-undo"></i>
        </button>
    </div>

    @if($description)
        <p class="text-sm text-gray-500 mt-2">
            <i class="fas fa-info-circle mr-1"></i> {{ $description }}
        </p>
    @endif

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror

    <!-- Live Preview Demo -->
    <div class="mt-3 p-3 bg-gray-50 rounded-md border border-gray-200">
        <p class="text-xs text-gray-600 mb-2 font-semibold">Preview:</p>
        <div class="flex items-center gap-3">
            <div :style="{ backgroundColor: color }"
                 class="px-4 py-2 rounded-md text-white font-semibold shadow-sm">
                Sample Button
            </div>
            <div :style="{ color: color }"
                 class="px-4 py-2 font-semibold">
                Sample Text
            </div>
            <div :style="{ borderColor: color }"
                 class="px-4 py-2 border-2 rounded-md font-semibold">
                Sample Border
            </div>
        </div>
    </div>
</div>
