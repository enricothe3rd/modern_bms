@php
$links = config('sidebar.links');
$footerLinks = config('sidebar.footer_links');

// Helper function to safely get route or fallback
$getRoute = fn($link) => isset($link['route']) ? (Route::has($link['route']) ? route($link['route']) : '#') : '#';
@endphp

<aside class="w-64 bg-white border-r border-gray-200 flex flex-col min-h-screen shadow-lg font-inter">
    <!-- Logo -->
    <div class="p-6 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-extrabold text-gray-900">BudgetPro</h1>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        @foreach ($links as $link)
            @php
                $href = $getRoute($link);
                $active = isset($link['route']) && request()->routeIs($link['route']);
                $iconPath = $link['icon'] ?? null;
            @endphp

            @if(isset($link['children']) && is_array($link['children']))
                <div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full gap-3 px-4 py-2 rounded-xl text-gray-700 hover:bg-gray-100 font-medium transition text-sm">
                        <span class="flex items-center gap-3">
                            @if($iconPath)
                                @php
                                    $svgFile = public_path($iconPath);
                                    $svgContent = file_exists($svgFile) ? file_get_contents($svgFile) : null;
                                @endphp
                                @if($svgContent)
                                    <span class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-blue-600' : 'text-gray-500' }}">
                                        {!! $svgContent !!}
                                    </span>
                                @endif
                            @endif
                            {{ $link['label'] }}
                        </span>
                        <svg :class="{'rotate-90 text-blue-600': open, 'text-gray-400': !open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="ml-4 space-y-1 border-l border-gray-200 pl-2">
                        @foreach($link['children'] as $child)
                            @php
                                $childHref = $getRoute($child);
                                $childActive = isset($child['route']) && request()->routeIs($child['route']);
                            @endphp
                            <a href="{{ $childHref }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl transition text-sm truncate
                               {{ $childActive ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $childActive ? 'bg-blue-600' : 'bg-gray-300' }} mr-1 flex-shrink-0"></span>
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $href }}" class="flex items-center gap-3 px-4 py-2 rounded-xl transition text-sm font-medium truncate
                    {{ $active ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    @if($iconPath)
                        @php
                            $svgFile = public_path($iconPath);
                            $svgContent = file_exists($svgFile) ? file_get_contents($svgFile) : null;
                        @endphp
                        @if($svgContent)
                            <span class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-blue-600' : 'text-gray-500' }}">
                                {!! $svgContent !!}
                            </span>
                        @endif
                    @endif
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach
    </nav>

    <!-- Footer -->
    <div class="border-t border-gray-200 p-4 space-y-2">
        @foreach ($footerLinks as $link)
            @php
                $footerHref = $getRoute($link);
                $iconPath = $link['icon'] ?? null;
            @endphp
            <a href="{{ $footerHref }}" class="flex items-center gap-3 px-4 py-2 rounded-xl transition text-sm font-medium truncate text-gray-600 hover:bg-gray-100 hover:text-gray-800">
                @if($iconPath)
                    @php
                        $svgFile = public_path($iconPath);
                        $svgContent = file_exists($svgFile) ? file_get_contents($svgFile) : null;
                    @endphp
                    @if($svgContent)
                        <span class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-blue-600' : 'text-gray-500' }}">
                            {!! $svgContent !!}
                        </span>
                    @endif
                @endif
                {{ $link['label'] }}
            </a>
        @endforeach
    </div>
</aside>
