@props(['name' => 'add_ons', 'addons' => []])

<div x-data="addonBuilder({{ json_encode($addons) }})" class="space-y-4">
    <!-- Add-On Items -->
    <template x-for="(addon, index) in items" :key="index">
        <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-3">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-gray-700">Add-On <span x-text="index + 1"></span></h4>
                <button type="button" @click="removeItem(index)"
                        class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i>Remove
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="addon.name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                           placeholder="e.g., Extra Hydration Boost">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" x-model="addon.price" step="0.01" min="0" required
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description (optional)
                </label>
                <textarea x-model="addon.description" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                          placeholder="Brief description of this add-on..."></textarea>
            </div>
        </div>
    </template>

    <!-- Add Add-On Button -->
    <button type="button" @click="addItem"
            class="w-full border-2 border-dashed border-gray-300 rounded-lg py-3 px-4 text-gray-600 hover:border-abs-primary hover:text-abs-primary transition-colors">
        <i class="fas fa-plus mr-2"></i>Add Add-On
    </button>

    <!-- Hidden input with JSON data -->
    <input type="hidden" :name="'{{ $name }}'" :value="JSON.stringify(items)">
</div>

<script>
function addonBuilder(initialAddons) {
    return {
        items: initialAddons && initialAddons.length > 0 ? initialAddons : [],

        addItem() {
            this.items.push({
                name: '',
                price: '',
                description: ''
            });
        },

        removeItem(index) {
            if (confirm('Remove this add-on?')) {
                this.items.splice(index, 1);
            }
        }
    }
}
</script>
