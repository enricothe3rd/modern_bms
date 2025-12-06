@php
use Illuminate\Support\Str;
@endphp

<x-app-layout>
    @section('title', 'Role Permissions')

<x-dashboard-header
    title="Role Permissions"
    subtitle="Manage permissions for user roles"
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">Role Permissions</h2>
            <p class="text-gray-600 mt-1">Assign specific permissions to user roles</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('management.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                ← Back to Management
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5 hover:shadow-2xl transition-all duration-300">
                <!-- Role Header -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $role->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $role->description }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Current Permissions -->
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Current Permissions:</h4>
                    @if($role->permissions->count() > 0)
                        <div class="space-y-1">
                            @foreach($role->permissions as $permission)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $availablePermissions[$permission->permission_name] ?? $permission->permission_name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-500 italic">No permissions assigned</p>
                    @endif
                </div>

                <!-- User Count -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Users with this role:</span>
                        <span class="font-semibold text-gray-900">{{ $role->users->count() }}</span>
                    </div>
                </div>

                <!-- Manage Button -->
                <button type="button" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition manage-permissions-btn"
                    data-role-id="{{ $role->id }}"
                    data-role-name="{{ $role->name }}">
                    Manage Permissions
                </button>
            </div>
        @endforeach
    </div>

</div>

<!-- Permissions Management Modal -->
<div id="permissionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-6 relative max-h-[90vh] overflow-y-auto">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="modalTitle">Manage Permissions</h2>

        <!-- Form -->
        <form id="permissionsForm" method="POST" action="{{ route('role-permissions.store') }}">
            @csrf
            <input type="hidden" id="roleIdInput" name="role_id" value="">

            <!-- Permissions by Module -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Permissions by Module</h3>
                
                <!-- Module Selection Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        @foreach($groupedPermissions as $moduleName => $moduleData)
                            <button type="button" 
                                class="module-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $loop->first ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                data-module="{{ Str::slug($moduleName) }}"
                                {{ $loop->first ? 'data-active="true"' : '' }}>
                                {{ $moduleName }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Module Content -->
                @foreach($groupedPermissions as $moduleName => $moduleData)
                    @php
                        $moduleSlug = Str::slug($moduleName);
                        $colorClasses = [
                            'blue' => 'bg-blue-50 border-blue-200 text-blue-800',
                            'green' => 'bg-green-50 border-green-200 text-green-800',
                            'purple' => 'bg-purple-50 border-purple-200 text-purple-800',
                            'indigo' => 'bg-indigo-50 border-indigo-200 text-indigo-800',
                            'orange' => 'bg-orange-50 border-orange-200 text-orange-800',
                            'teal' => 'bg-teal-50 border-teal-200 text-teal-800',
                        ];
                        $moduleColorClass = $colorClasses[$moduleData['color']] ?? $colorClasses['blue'];
                    @endphp
                    
                    <div class="module-content {{ $loop->first ? '' : 'hidden' }}" data-module="{{ $moduleSlug }}">
                        <!-- Module Header -->
                        <div class="mb-4 p-4 rounded-lg {{ $moduleColorClass }} border">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($moduleData['icon'] === 'building-office')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    @elseif($moduleData['icon'] === 'banknotes')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($moduleData['icon'] === 'calculator')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($moduleData['icon'] === 'users')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                        </svg>
                                    @elseif($moduleData['icon'] === 'document-text')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @elseif($moduleData['icon'] === 'chart-bar')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold">{{ $moduleName }}</h4>
                                    <p class="text-sm opacity-75">{{ count($moduleData['permissions']) }} permissions available</p>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions in this module -->
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($moduleData['permissions'] as $permissionKey => $permissionDescription)
                                <label class="permission-item group cursor-pointer">
                                    <input type="checkbox" 
                                        name="permissions[]" 
                                        value="{{ $permissionKey }}"
                                        class="permission-checkbox sr-only">
                                    <div class="border-2 border-gray-200 p-4 rounded-lg transition-all duration-200 group-hover:border-{{ $moduleData['color'] }}-300 group-hover:shadow checkbox-container">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-5 h-5 border-2 border-gray-300 rounded checkbox-visual">
                                                    <svg class="w-3 h-3 text-white hidden checkmark" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">{{ $permissionDescription }}</p>
                                                <p class="text-xs text-gray-500">{{ $permissionKey }}</p>
                                                @if(Str::contains($permissionKey, 'delete'))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        High Risk
                                                    </span>
                                                @elseif(Str::contains($permissionKey, 'manage'))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        Management
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        Standard
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Permission Summary -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700">Selected Permissions</h4>
                        <p class="text-xs text-gray-500">Total permissions selected for this role</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-indigo-600" id="selectedCount">0</div>
                        <div class="text-xs text-gray-500">permissions</div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-3 flex space-x-2">
                    <button type="button" id="selectAllBtn" class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200 transition">
                        Select All in Current Tab
                    </button>
                    <button type="button" id="clearAllBtn" class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition">
                        Clear All in Current Tab
                    </button>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <span id="totalSelectedText">No permissions selected</span>
                </div>
                <div class="flex space-x-2">
                    <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg shadow-md transition">Update Permissions</button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('permissionsModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalTitle = document.getElementById('modalTitle');
    const roleIdInput = document.getElementById('roleIdInput');

    // Tab functionality
    function initializeTabs() {
        document.querySelectorAll('.module-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const targetModule = this.dataset.module;
                
                // Update tab styles
                document.querySelectorAll('.module-tab').forEach(t => {
                    t.classList.remove('border-indigo-500', 'text-indigo-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                    t.removeAttribute('data-active');
                });
                
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-indigo-500', 'text-indigo-600');
                this.setAttribute('data-active', 'true');
                
                // Show/hide module content
                document.querySelectorAll('.module-content').forEach(content => {
                    if (content.dataset.module === targetModule) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });
        });
    }

    // Show modal
    document.querySelectorAll('.manage-permissions-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const roleId = this.dataset.roleId;
            const roleName = this.dataset.roleName;

            modalTitle.textContent = `Manage Permissions - ${roleName}`;
            roleIdInput.value = roleId;

            // Fetch current permissions and populate checkboxes
            fetch(`/role-permissions/${roleId}`)
                .then(response => response.json())
                .then(data => {
                    // Clear all checkboxes
                    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check assigned permissions
                    data.assignedPermissions.forEach(permission => {
                        const checkbox = document.querySelector(`input[value="${permission}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    updateCheckboxStyles();
                    updatePermissionCounts();
                    modal.classList.remove('hidden');
                });
        });
    });

    // Hide modal
    function hideModal() {
        modal.classList.add('hidden');
        // Reset to first tab
        const firstTab = document.querySelector('.module-tab[data-active="true"]');
        if (firstTab) {
            firstTab.click();
        }
    }

    closeModal.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    // Checkbox styling and interaction
    document.querySelectorAll('.permission-item').forEach(item => {
        item.addEventListener('click', function() {
            const checkbox = this.querySelector('.permission-checkbox');
            checkbox.checked = !checkbox.checked;
            updateCheckboxStyles();
            updatePermissionCounts();
        });
    });

    function updateCheckboxStyles() {
        document.querySelectorAll('.permission-item').forEach(item => {
            const checkbox = item.querySelector('.permission-checkbox');
            const container = item.querySelector('.checkbox-container');
            const visual = item.querySelector('.checkbox-visual');
            const checkmark = item.querySelector('.checkmark');

            if (checkbox.checked) {
                container.classList.remove('border-gray-200');
                container.classList.add('border-indigo-500', 'bg-indigo-50');
                visual.classList.remove('border-gray-300');
                visual.classList.add('border-indigo-500', 'bg-indigo-500');
                checkmark.classList.remove('hidden');
            } else {
                container.classList.remove('border-indigo-500', 'bg-indigo-50');
                container.classList.add('border-gray-200');
                visual.classList.remove('border-indigo-500', 'bg-indigo-500');
                visual.classList.add('border-gray-300');
                checkmark.classList.add('hidden');
            }
        });
    }

    function updatePermissionCounts() {
        // Update tab counts
        document.querySelectorAll('.module-tab').forEach(tab => {
            const moduleSlug = tab.dataset.module;
            const moduleContent = document.querySelector(`.module-content[data-module="${moduleSlug}"]`);
            if (moduleContent) {
                const totalPermissions = moduleContent.querySelectorAll('.permission-checkbox').length;
                const checkedPermissions = moduleContent.querySelectorAll('.permission-checkbox:checked').length;
                
                // Update tab text to show count
                const tabText = tab.textContent.split(' (')[0]; // Remove existing count
                if (checkedPermissions > 0) {
                    tab.textContent = `${tabText} (${checkedPermissions}/${totalPermissions})`;
                    tab.classList.add('font-semibold');
                } else {
                    tab.textContent = tabText;
                    tab.classList.remove('font-semibold');
                }
            }
        });

        // Update total selected count
        const totalSelected = document.querySelectorAll('.permission-checkbox:checked').length;
        const selectedCountEl = document.getElementById('selectedCount');
        const totalSelectedTextEl = document.getElementById('totalSelectedText');
        
        if (selectedCountEl) {
            selectedCountEl.textContent = totalSelected;
        }
        
        if (totalSelectedTextEl) {
            if (totalSelected === 0) {
                totalSelectedTextEl.textContent = 'No permissions selected';
            } else if (totalSelected === 1) {
                totalSelectedTextEl.textContent = '1 permission selected';
            } else {
                totalSelectedTextEl.textContent = `${totalSelected} permissions selected`;
            }
        }
    }

    // Quick action buttons
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const activeTab = document.querySelector('.module-tab[data-active="true"]');
        if (activeTab) {
            const moduleSlug = activeTab.dataset.module;
            const moduleContent = document.querySelector(`.module-content[data-module="${moduleSlug}"]`);
            if (moduleContent) {
                moduleContent.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateCheckboxStyles();
                updatePermissionCounts();
            }
        }
    });

    document.getElementById('clearAllBtn').addEventListener('click', function() {
        const activeTab = document.querySelector('.module-tab[data-active="true"]');
        if (activeTab) {
            const moduleSlug = activeTab.dataset.module;
            const moduleContent = document.querySelector(`.module-content[data-module="${moduleSlug}"]`);
            if (moduleContent) {
                moduleContent.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateCheckboxStyles();
                updatePermissionCounts();
            }
        }
    });

    // Initialize tabs and checkbox styles
    initializeTabs();
    updateCheckboxStyles();
    updatePermissionCounts();

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });

    // Close modal on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
});
</script>

</x-app-layout>