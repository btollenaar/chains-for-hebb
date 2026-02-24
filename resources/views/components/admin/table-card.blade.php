@props(['item', 'route', 'fields' => [], 'actions' => []])

<a href="{{ route($route, $item) }}"
   class="block bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-200"
   aria-label="View {{ $item->id }}">

    <div class="space-y-3">
        @foreach($fields as $field)
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                    {{ $field['label'] }}
                </p>
                <div class="mt-1">
                    {!! $field['render']($item) !!}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Actions -->
    @if(count($actions) > 0)
        <div class="mt-4 pt-4 border-t flex justify-end gap-3">
            @foreach($actions as $action)
                <span class="text-{{ $action['color'] ?? 'blue' }}-600 hover:text-{{ $action['color'] ?? 'blue' }}-800 transition-colors duration-200"
                      aria-label="{{ $action['label'] }}">
                    <i class="fas {{ $action['icon'] }}"></i>
                </span>
            @endforeach
        </div>
    @endif
</a>
