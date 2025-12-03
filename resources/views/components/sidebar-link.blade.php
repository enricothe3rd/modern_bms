@props(['href' => '#', 'icon' => null, 'label', 'active' => false, 'size' => 24])

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => 'flex items-center gap-3 px-4 py-2 rounded-xl transition text-sm font-medium truncate ' .
                  ($active
                      ? 'bg-blue-50 text-blue-700 font-semibold'
                      : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900')
   ]) }}>

    @if($icon)
        <span class="w-6 h-6 flex items-center justify-center rounded-full {{ $active ? 'bg-blue-100' : 'bg-gray-100' }}">
            <img src="{{ asset($icon) }}" class="w-4 h-4" alt="{{ $label }}">
        </span>
    @endif

    <span>{{ $label }}</span>
</a>
