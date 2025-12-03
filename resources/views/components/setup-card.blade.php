{{-- resources/views/components/setup-card.blade.php --}}

@props(['title', 'description', 'route', 'color' => 'indigo', 'icon'])

<a href="{{ $route }}"
   class="block bg-white shadow-xl rounded-2xl p-6 transition duration-300 ease-in-out transform hover:scale-[1.02] hover:shadow-2xl ring-1 ring-gray-900/5">
    <div class="flex items-start">
        {{-- Icon Container: Dynamically uses the provided color --}}
        <div class="flex-shrink-0 bg-{{ $color }}-500 rounded-full p-3">
            {{-- Icon Slot: Allows custom SVG or image to be passed in --}}
            {{ $icon }}
        </div>
        <div class="ml-4">
            <h2 class="text-xl font-bold text-gray-900">{{ $title }}</h2>
            <p class="mt-1 text-gray-600 text-sm">{{ $description }}</p>
        </div>
    </div>
    <div class="mt-4 flex justify-end">
        {{-- Call to Action: Dynamically uses the provided color --}}
        <span class="text-{{ $color }}-600 hover:text-{{ $color }}-800 font-semibold text-sm">
            Go to Setup &rarr;
        </span>
    </div>
</a>
