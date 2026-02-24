@props(['name' => 'packages', 'packages' => []])

<div x-data="packageBuilder({{ json_encode($packages) }})" class="space-y-4">
    <!-- Package Items -->
    <template x-for="(pkg, index) in items" :key="index">
        <div class="bg-white border-2 border-gray-300 rounded-lg p-5 space-y-4">
            <div class="flex items-center justify-between mb-2 pb-2 border-b">
                <h4 class="font-bold text-gray-800">Package <span x-text="index + 1"></span></h4>
                <button type="button" @click="removeItem(index)"
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                    <i class="fas fa-trash mr-1"></i>Remove Package
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Package Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="pkg.name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                           placeholder="e.g., Myers Cocktail">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" x-model="pkg.price" step="0.01" min="0" required
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea x-model="pkg.description" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                          placeholder="What makes this package special?"></textarea>
            </div>

            <!-- Ingredients -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700">
                        <i class="fas fa-flask mr-1 text-gray-500"></i>Ingredients
                    </label>
                    <button type="button" @click="addIngredient(index)"
                            class="text-xs text-abs-primary hover:text-gray-700">
                        <i class="fas fa-plus mr-1"></i>Add Ingredient
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="(ingredient, iIndex) in (pkg.ingredients || [])" :key="iIndex">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="pkg.ingredients[iIndex]"
                                   class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                                   placeholder="Ingredient name">
                            <button type="button" @click="removeIngredient(index, iIndex)"
                                    class="text-red-600 hover:text-red-800 px-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                    <template x-if="!pkg.ingredients || pkg.ingredients.length === 0">
                        <p class="text-sm text-gray-500 italic">No ingredients added yet</p>
                    </template>
                </div>
            </div>

            <!-- Benefits -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700">
                        <i class="fas fa-check-circle mr-1 text-gray-500"></i>Benefits
                    </label>
                    <button type="button" @click="addBenefit(index)"
                            class="text-xs text-abs-primary hover:text-gray-700">
                        <i class="fas fa-plus mr-1"></i>Add Benefit
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="(benefit, bIndex) in (pkg.benefits || [])" :key="bIndex">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="pkg.benefits[bIndex]"
                                   class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                                   placeholder="Benefit description">
                            <button type="button" @click="removeBenefit(index, bIndex)"
                                    class="text-red-600 hover:text-red-800 px-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                    <template x-if="!pkg.benefits || pkg.benefits.length === 0">
                        <p class="text-sm text-gray-500 italic">No benefits added yet</p>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- Add Package Button -->
    <button type="button" @click="addItem"
            class="w-full border-2 border-dashed border-gray-300 rounded-lg py-3 px-4 text-gray-600 hover:border-abs-primary hover:text-abs-primary transition-colors">
        <i class="fas fa-plus mr-2"></i>Add Treatment Package
    </button>

    <!-- Hidden input with JSON data -->
    <input type="hidden" :name="'{{ $name }}'" :value="JSON.stringify(items)">
</div>

<script>
function packageBuilder(initialPackages) {
    return {
        items: initialPackages && initialPackages.length > 0 ? initialPackages : [],

        addItem() {
            this.items.push({
                name: '',
                price: '',
                description: '',
                ingredients: [],
                benefits: []
            });
        },

        removeItem(index) {
            if (confirm('Remove this package?')) {
                this.items.splice(index, 1);
            }
        },

        addIngredient(pkgIndex) {
            if (!this.items[pkgIndex].ingredients) {
                this.items[pkgIndex].ingredients = [];
            }
            this.items[pkgIndex].ingredients.push('');
        },

        removeIngredient(pkgIndex, ingredientIndex) {
            this.items[pkgIndex].ingredients.splice(ingredientIndex, 1);
        },

        addBenefit(pkgIndex) {
            if (!this.items[pkgIndex].benefits) {
                this.items[pkgIndex].benefits = [];
            }
            this.items[pkgIndex].benefits.push('');
        },

        removeBenefit(pkgIndex, benefitIndex) {
            this.items[pkgIndex].benefits.splice(benefitIndex, 1);
        }
    }
}
</script>
