@php
$links = config('sidebar.links');
$footerLinks = config('sidebar.footer_links');

// Helper function to safely get route or fallback
$getRoute = fn($link) => isset($link['route']) ? (Route::has($link['route']) ? route($link['route']) : '#') : '#';

// Helper function to get Heroicon SVG
$getHeroicon = function($iconName, $class = 'w-5 h-5') {
    $icons = [
        'home' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path></svg>',
        'document-chart-bar' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path></svg>',
        'cog-6-tooth' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"></path></svg>',
        'user' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>',
        'adjustments-horizontal' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m0 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"></path></svg>',
        'user-circle' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'question-mark-circle' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path></svg>',
        'arrow-right-on-rectangle' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path></svg>',
        'arrows-right-left' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path></svg>',
        'currency-dollar' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.467-.22-2.121-.659-1.172-.879-1.172-2.303 0-3.182C10.536 7.78 11.268 7.5 12 7.5c.847 0 1.664.28 2.121.659M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'document-text' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path></svg>',
        'calendar' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path></svg>',
    ];
    return $icons[$iconName] ?? '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>';
};
@endphp

<aside class="w-64 bg-white border-r border-gray-200 flex flex-col min-h-screen shadow-lg font-inter"
    <!-- Logo -->
    <div class="p-6 flex-shrink-0 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">BudgetPro</h1>
                <p class="text-xs text-gray-500">Budget Management System</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    @auth
    <div class="p-4 border-b border-gray-100 bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center ring-2 ring-white shadow-sm">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <span class="text-sm font-semibold text-gray-600">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    @endif
                </div>
                <!-- Online status indicator -->
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                @if(auth()->user()->isSuperAdmin())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-red-100 to-pink-100 text-red-800 mt-1 shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Super Admin
                    </span>
                @elseif(auth()->user()->role)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 mt-1 shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ auth()->user()->role->name }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endauth

    <!-- Quick Actions -->
    @auth
    <div class="px-4 py-3 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center p-2 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200 group">
                <span class="text-blue-600 group-hover:text-blue-700">
                    {!! $getHeroicon('user', 'w-4 h-4') !!}
                </span>
            </a>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage_role_permissions'))
                <a href="{{ route('management.index') }}" class="flex items-center justify-center p-2 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200 group">
                    <span class="text-green-600 group-hover:text-green-700">
                        {!! $getHeroicon('cog-6-tooth', 'w-4 h-4') !!}
                    </span>
                </a>
            @endif
        </div>
    </div>
    @endauth

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        @foreach ($links as $link)
            @php
                $href = $getRoute($link);
                $active = isset($link['route']) && request()->routeIs($link['route']);
                $iconName = $link['icon'] ?? null;
            @endphp

            @if(isset($link['children']) && is_array($link['children']))
                <div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-all duration-200 text-sm group">
                        <span class="flex items-center gap-3">
                            @if($iconName)
                                <span class="flex-shrink-0 {{ $active ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                    {!! $getHeroicon($iconName) !!}
                                </span>
                            @endif
                            {{ $link['label'] }}
                        </span>
                        <svg :class="{'rotate-90 text-blue-600': open, 'text-gray-400': !open}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="ml-8 space-y-1 border-l-2 border-gray-100 pl-3">
                        @foreach($link['children'] as $child)
                            @php
                                $childHref = $getRoute($child);
                                $childActive = isset($child['route']) && request()->routeIs($child['route']);
                            @endphp
                            <a href="{{ $childHref }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm truncate group
                               {{ $childActive ? 'bg-blue-50 text-blue-700 font-semibold border-l-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $childActive ? 'bg-blue-600' : 'bg-gray-300 group-hover:bg-gray-400' }} flex-shrink-0"></span>
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $href }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 text-sm font-medium truncate group
                    {{ $active ? 'bg-blue-50 text-blue-700 font-semibold border-l-2 border-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    @if($iconName)
                        <span class="flex-shrink-0 {{ $active ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                            {!! $getHeroicon($iconName) !!}
                        </span>
                    @endif
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach
    </nav>

    <!-- System Status -->
    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span>System Status</span>
            <div class="flex items-center gap-1">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span>Online</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-gray-100 p-4 space-y-1">
        @foreach ($footerLinks as $link)
            @php
                $iconName = $link['icon'] ?? null;
                $isLogout = isset($link['action']) && $link['action'] === 'logout';
                $footerHref = $isLogout ? '#' : (isset($link['href']) ? $link['href'] : $getRoute($link));
            @endphp
            
            @if($isLogout)
                <button onclick="confirmLogout()" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 text-sm font-medium truncate text-gray-600 hover:bg-red-50 hover:text-red-700 w-full text-left group">
                    @if($iconName)
                        <span class="flex-shrink-0 text-gray-400 group-hover:text-red-600">
                            {!! $getHeroicon($iconName) !!}
                        </span>
                    @endif
                    {{ $link['label'] }}
                </button>
            @else
                <a href="{{ $footerHref }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 text-sm font-medium truncate text-gray-600 hover:bg-gray-50 hover:text-gray-900 group">
                    @if($iconName)
                        <span class="flex-shrink-0 text-gray-400 group-hover:text-gray-600">
                            {!! $getHeroicon($iconName) !!}
                        </span>
                    @endif
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</aside>

<!-- Logout Confirmation Modal -->
<x-confirmation-modal 
    id="logoutModal"
    title="Confirm Logout"
    message="Are you sure you want to logout? You will need to sign in again to access your account."
    confirmText="Logout"
    cancelText="Cancel"
    confirmClass="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<script>
function confirmLogout() {
    showConfirmation({
        title: 'Confirm Logout',
        message: 'Are you sure you want to logout? You will need to sign in again to access your account.',
        onConfirm: function() {
            document.getElementById('logout-form').submit();
        }
    });
}
</script>
