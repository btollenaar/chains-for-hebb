@props(['categories', 'selectedIds' => [], 'primaryId' => null,
        'name' => 'category_ids', 'primaryName' => 'primary_category_id',
        'depth' => 0, 'parentId' => null])

@foreach($categories->where('parent_id', $parentId) as $cat)
<div x-data="{
    expanded: {{ $cat->children->isNotEmpty() ? 'false' : 'true' }},
    checked: {{ in_array($cat->id, $selectedIds) ? 'true' : 'false' }}
}" class="border-l-2" :class="checked ? 'border-admin-teal' : 'border-gray-200'"
   style="margin-left: {{ $depth * 1.5 }}rem">

    <div class="flex items-center py-2 px-2 hover:bg-gray-50">
        @if($cat->children->isNotEmpty())
            <button type="button" @click="expanded = !expanded" class="w-6 h-6 flex items-center justify-center mr-2 flex-shrink-0">
                <i class="fas fa-chevron-right text-xs transition-transform"
                   :class="expanded ? 'rotate-90' : ''"></i>
            </button>
        @else
            <span class="w-6 h-6 mr-2 flex-shrink-0"></span>
        @endif

        <label class="flex-1 flex items-center cursor-pointer group">
            <input type="checkbox" name="{{ $name }}[]" value="{{ $cat->id }}"
                   x-model="checked" {{ in_array($cat->id, $selectedIds) ? 'checked' : '' }}
                   class="w-4 h-4 text-admin-teal rounded">
            <span class="ml-2 text-sm" :class="checked ? 'font-semibold' : ''">
                {{ $cat->name }}
            </span>
            @if($cat->parent)
                <span class="ml-2 text-xs text-gray-400">({{ $cat->getFullPath(' > ') }})</span>
            @endif
        </label>

        <label x-show="checked" x-transition class="ml-4 flex items-center cursor-pointer">
            <input type="radio" name="{{ $primaryName }}" value="{{ $cat->id }}"
                   {{ $cat->id == $primaryId ? 'checked' : '' }}
                   class="w-4 h-4 text-amber-600">
            <span class="ml-1 text-xs text-gray-500">Primary</span>
        </label>
    </div>

    @if($cat->children->isNotEmpty())
        <div x-show="expanded" x-transition x-cloak>
            <x-admin.category-tree-checkbox :categories="$categories"
                :selectedIds="$selectedIds" :primaryId="$primaryId"
                :name="$name" :primaryName="$primaryName"
                :depth="$depth + 1" :parentId="$cat->id" />
        </div>
    @endif
</div>
@endforeach
