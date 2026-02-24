@props(['name' => 'faqs', 'faqs' => []])

<div x-data="faqBuilder({{ json_encode($faqs) }})" class="space-y-4">
    <!-- FAQ Items -->
    <template x-for="(faq, index) in items" :key="index">
        <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-3">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-gray-700">FAQ <span x-text="index + 1"></span></h4>
                <button type="button" @click="removeItem(index)"
                        class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i>Remove
                </button>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Question <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="faq.question" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                       placeholder="What should customers know?">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Answer <span class="text-red-500">*</span>
                </label>
                <textarea x-model="faq.answer" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                          placeholder="Provide a clear, helpful answer..."></textarea>
            </div>
        </div>
    </template>

    <!-- Add FAQ Button -->
    <button type="button" @click="addItem"
            class="w-full border-2 border-dashed border-gray-300 rounded-lg py-3 px-4 text-gray-600 hover:border-abs-primary hover:text-abs-primary transition-colors">
        <i class="fas fa-plus mr-2"></i>Add FAQ
    </button>

    <!-- Hidden input with JSON data -->
    <input type="hidden" :name="'{{ $name }}'" :value="JSON.stringify(items)">
</div>

<script>
function faqBuilder(initialFaqs) {
    return {
        items: initialFaqs && initialFaqs.length > 0 ? initialFaqs : [],

        addItem() {
            this.items.push({
                question: '',
                answer: ''
            });
        },

        removeItem(index) {
            if (confirm('Remove this FAQ?')) {
                this.items.splice(index, 1);
            }
        }
    }
}
</script>
