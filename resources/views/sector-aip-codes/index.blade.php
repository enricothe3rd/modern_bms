@php
use Illuminate\Support\Str;
@endphp

<x-app-layout>
    @section('title', 'Sector AIP Codes')

<x-dashboard-header
    title="Sector AIP Codes"
    subtitle="Manage AIP codes for {{ $sector->name }}"
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">AIP Codes</h2>
            <p class="text-gray-600 mt-1">Sector: <span class="font-semibold text-purple-700">{{ $sector->name }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sectors.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                ← Back to Sectors
            </a>
            @if(auth()->user()->canManageDepartments())
                <button id="addAipBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                    + Add AIP Code
                </button>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- AIP Codes Table -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6">
        <table id="aipTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AIP Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($aipCodes as $aipCode)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $aipCode->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-mono font-semibold text-gray-900">{{ $aipCode->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">
                                {{ $aipCode->description ?: '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($aipCode->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $aipCode->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if(auth()->user()->canManageDepartments())
                                    <button class="editBtn text-indigo-600 hover:text-indigo-700 p-1 rounded-full hover:bg-indigo-50 transition duration-150 ease-in-out"
                                        data-id="{{ $aipCode->id }}"
                                        data-code="{{ $aipCode->code }}"
                                        data-description="{{ $aipCode->description }}"
                                        data-is-active="{{ $aipCode->is_active }}"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <button class="toggleBtn text-{{ $aipCode->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $aipCode->is_active ? 'yellow' : 'green' }}-700 p-1 rounded-full hover:bg-{{ $aipCode->is_active ? 'yellow' : 'green' }}-50 transition duration-150 ease-in-out"
                                        data-id="{{ $aipCode->id }}"
                                        data-is-active="{{ $aipCode->is_active }}"
                                        data-code="{{ $aipCode->code }}"
                                        title="{{ $aipCode->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                @endif

                                @if(auth()->user()->canDeleteDepartments())
                                    <form action="{{ route('sector-aip-codes.destroy', [$sector->id, $aipCode->id]) }}" method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out delete-btn"
                                            data-aip-code="{{ $aipCode->code }}"
                                            title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">No AIP codes found</p>
                                <p class="text-sm">Get started by creating your first AIP code.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    @if($aipCodes->count() > 0)
    // Initialize DataTable only if there's data
    const table = $('#aipTable').DataTable({
        dom: "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'<'buttons'B><'search-wrapper'f>>" +
             "rt" +
             "<'flex flex-col sm:flex-row justify-between items-center mt-4 gap-4'<'info'i><'pagination'p>>",

        buttons: {
            dom: {
                button: {
                    className: 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-150 ease-in-out'
                }
            },
            buttons: ['excel', 'csv', 'pdf', 'print']
        },

        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        columnDefs: [
            { targets: -1, orderable: false, searchable: false }
        ],
        language: {
            search: "",
            searchPlaceholder: "Search AIP codes...",
            paginate: {
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            }
        },
        order: [[1, 'asc']],
        drawCallback: function() {
            $('.paginate_button').addClass('px-3 py-1 text-sm border rounded-lg hover:bg-purple-50 text-purple-600 border-purple-200 transition duration-150 ease-in-out');
            $('.paginate_button.current').addClass('bg-purple-600 text-white hover:bg-purple-700').removeClass('bg-purple-50 text-purple-600');
            $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed text-gray-400 border-gray-200').removeClass('text-purple-600 border-purple-200');
        }
    });

    $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-400 transition duration-150 ease-in-out').removeClass('form-control');
    $('.dt-buttons').addClass('flex flex-wrap gap-2');
    $('.dataTables_info').addClass('text-sm text-gray-600');
    $('.dataTables_paginate').addClass('flex gap-1');
    @endif
});
</script>
@endpush

<!-- Add/Edit Modal -->
<div id="aipModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add AIP Code</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('sector-aip-codes.store', $sector->id) }}">
            @csrf

            <!-- Code -->
            <div class="mb-4">
                <x-input-label value="AIP Code" />
                <x-input type="text" name="code" id="codeInput" placeholder="e.g., 111-A20011" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('code')" class="mt-1" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <x-input-label value="Description (Optional)" />
                <x-input type="text" name="description" id="descriptionInput" placeholder="Optional description" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="isActiveInput" value="1" checked class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Active</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" id="submitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg shadow-md transition">Add</button>
            </div>
        </form>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<x-confirmation-modal 
    id="deleteAipModal"
    title="Confirm Delete"
    message="Are you sure you want to delete this AIP code? This action cannot be undone."
    confirmText="Delete"
    cancelText="Cancel"
    confirmClass="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('aipModal');
    const addBtn = document.getElementById('addAipBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const codeInput = document.getElementById('codeInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const isActiveInput = document.getElementById('isActiveInput');

    // Show modal if there are validation errors
    @if ($errors->any())
        modal.classList.remove('hidden');
    @endif

    // Show modal for Add
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.remove('hidden');
            formTitle.textContent = 'Add AIP Code';
            submitBtn.textContent = 'Add';
            formElement.action = "{{ route('sector-aip-codes.store', $sector->id) }}";
            codeInput.value = '';
            descriptionInput.value = '';
            isActiveInput.checked = true;

            const putMethod = formElement.querySelector('input[name="_method"]');
            if (putMethod) putMethod.remove();
        });
    }

    // Cancel / Close modal
    function hideModal() {
        modal.classList.add('hidden');
    }

    cancelBtn.addEventListener('click', hideModal);
    closeModal.addEventListener('click', hideModal);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Edit buttons
    document.body.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.editBtn');
        if (editBtn) {
            e.preventDefault();
            
            modal.classList.remove('hidden');
            formTitle.textContent = 'Edit AIP Code';
            submitBtn.textContent = 'Update';
            formElement.action = `/sector-aip-codes/{{ $sector->id }}/${editBtn.dataset.id}`;
            codeInput.value = editBtn.dataset.code;
            descriptionInput.value = editBtn.dataset.description || '';
            isActiveInput.checked = editBtn.dataset.isActive === '1';

            if (!formElement.querySelector('input[name="_method"]')) {
                const putMethod = document.createElement('input');
                putMethod.type = 'hidden';
                putMethod.name = '_method';
                putMethod.value = 'PUT';
                formElement.appendChild(putMethod);
            }
        }
    });

    // Toggle status buttons
    document.body.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.toggleBtn');
        if (toggleBtn) {
            e.preventDefault();
            
            const aipId = toggleBtn.dataset.id;
            const isActive = toggleBtn.dataset.isActive === '1';
            const code = toggleBtn.dataset.code;
            
            showConfirmation({
                title: isActive ? 'Deactivate AIP Code' : 'Activate AIP Code',
                message: `Are you sure you want to ${isActive ? 'deactivate' : 'activate'} "${code}"?`,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/sector-aip-codes/{{ $sector->id }}/${aipId}/toggle`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';
                    
                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });

    // Delete confirmation
    document.body.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault();
            
            const aipCode = deleteBtn.getAttribute('data-aip-code');
            const form = deleteBtn.closest('.delete-form');
            
            showConfirmation({
                title: 'Confirm Delete',
                message: `Are you sure you want to delete AIP code "${aipCode}"? This action cannot be undone.`,
                onConfirm: function() {
                    form.submit();
                }
            });
        }
    });
});
</script>

</x-app-layout>
