@php
use Illuminate\Support\Str;
@endphp

<x-app-layout>
    @section('title', 'Sectors')

<x-dashboard-header
    title="Sectors"
    subtitle="Manage organizational sectors and divisions."
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">Sectors</h2>
            <p class="text-gray-600 mt-1">Manage organizational sectors and their departments</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('management.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                ← Back to Management
            </a>
            @if(auth()->user()->canManageDepartments())
                <button id="addSectorBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                    + Add Sector
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

    <!-- Sectors Table -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6">
        <table id="sectorTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sector Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AIP Codes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sectors as $sector)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $sector->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-4a1 1 0 011-1h2a1 1 0 011 1v4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $sector->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sector->aip_codes_count > 0 ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $sector->aip_codes_count }} {{ Str::plural('code', $sector->aip_codes_count) }}
                                </span>
                                @if(auth()->user()->canManageDepartments())
                                    <a href="{{ route('sector-aip-codes.index', $sector->id) }}" 
                                        class="text-purple-600 hover:text-purple-700 text-xs font-medium hover:underline"
                                        title="Manage AIP Codes">
                                        Manage
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sector->departments_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $sector->departments_count }} {{ Str::plural('department', $sector->departments_count) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $sector->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if(auth()->user()->canManageDepartments())
                                    <button class="editBtn text-indigo-600 hover:text-indigo-700 p-1 rounded-full hover:bg-indigo-50 transition duration-150 ease-in-out"
                                        data-id="{{ $sector->id }}"
                                        data-name="{{ $sector->name }}"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                @endif

                                @if(auth()->user()->canDeleteDepartments())
                                    <form action="{{ route('sectors.destroy', $sector->id) }}" method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out delete-btn"
                                            data-sector-name="{{ $sector->name }}"
                                            data-departments-count="{{ $sector->departments_count }}"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-4a1 1 0 011-1h2a1 1 0 011 1v4"></path>
                                </svg>
                                <p class="text-lg font-medium">No sectors found</p>
                                <p class="text-sm">Get started by creating your first sector.</p>
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
    // Initialize DataTable
    const table = $('#sectorTable').DataTable({
        dom: "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'<'buttons'B><'search-wrapper'f>>" +
             "rt" +
             "<'flex flex-col sm:flex-row justify-between items-center mt-4 gap-4'<'info'i><'pagination'p>>",

        buttons: {
            dom: {
                button: {
                    className: 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out'
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
            searchPlaceholder: "Search sectors...",
            paginate: {
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            }
        },
        order: [[1, 'asc']],
        drawCallback: function() {
            // Re-apply styles after each draw
            $('.paginate_button').addClass('px-3 py-1 text-sm border rounded-lg hover:bg-indigo-50 text-indigo-600 border-indigo-200 transition duration-150 ease-in-out');
            $('.paginate_button.current').addClass('bg-indigo-600 text-white hover:bg-indigo-700').removeClass('bg-indigo-50 text-indigo-600');
            $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed text-gray-400 border-gray-200').removeClass('text-indigo-600 border-indigo-200');
        }
    });

    // Style the search input and other elements
    $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 transition duration-150 ease-in-out').removeClass('form-control');
    $('.dt-buttons').addClass('flex flex-wrap gap-2');
    $('.dataTables_info').addClass('text-sm text-gray-600');
    $('.dataTables_paginate').addClass('flex gap-1');
});
</script>
@endpush

<!-- Modal Overlay -->
<div id="sectorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add Sector</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('sectors.store') }}">
            @csrf

            <!-- Name Row -->
            <div class="mb-4">
                <x-input-label value="Sector Name" />
                <x-input type="text" name="name" id="nameInput" placeholder="e.g., General Public Services" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">Add</button>
            </div>
        </form>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<x-confirmation-modal 
    id="deleteSectorModal"
    title="Confirm Delete"
    message="Are you sure you want to delete this sector? This action cannot be undone."
    confirmText="Delete"
    cancelText="Cancel"
    confirmClass="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('sectorModal');
    const addBtn = document.getElementById('addSectorBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const nameInput = document.getElementById('nameInput');

    // Show modal if there are validation errors
    @if ($errors->any())
        modal.classList.remove('hidden');
    @endif

    // Show modal if redirected from edit route
    @if(session('editSector'))
        const editSector = @json(session('editSector'));
        modal.classList.remove('hidden');
        formTitle.textContent = 'Edit Sector';
        submitBtn.textContent = 'Update';
        formElement.action = `/sectors/${editSector.id}`;
        nameInput.value = editSector.name;
        
        // Add PUT method
        if (!formElement.querySelector('input[name="_method"]')) {
            const putMethod = document.createElement('input');
            putMethod.type = 'hidden';
            putMethod.name = '_method';
            putMethod.value = 'PUT';
            formElement.appendChild(putMethod);
        }
    @endif

    // Show modal for Add
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            modal.classList.remove('hidden');
            formTitle.textContent = 'Add Sector';
            submitBtn.textContent = 'Add';
            formElement.action = "{{ route('sectors.store') }}";
            nameInput.value = '';

            // Remove old PUT method if exists
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

    // Edit buttons - Use event delegation to handle dynamically loaded content
    document.body.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.editBtn');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            modal.classList.remove('hidden');
            formTitle.textContent = 'Edit Sector';
            submitBtn.textContent = 'Update';
            formElement.action = `/sectors/${editBtn.dataset.id}`;
            nameInput.value = editBtn.dataset.name;

            // Add PUT method if not already
            if (!formElement.querySelector('input[name="_method"]')) {
                const putMethod = document.createElement('input');
                putMethod.type = 'hidden';
                putMethod.name = '_method';
                putMethod.value = 'PUT';
                formElement.appendChild(putMethod);
            }
        }
    });

    // Delete confirmation - Use event delegation
    document.body.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const sectorName = deleteBtn.getAttribute('data-sector-name');
            const departmentsCount = parseInt(deleteBtn.getAttribute('data-departments-count'));
            const form = deleteBtn.closest('.delete-form');
            
            let message = `Are you sure you want to delete "${sectorName}"?`;
            if (departmentsCount > 0) {
                message = `Cannot delete "${sectorName}" because it has ${departmentsCount} department(s) assigned to it. Please reassign or delete the departments first.`;
            } else {
                message += ' This action cannot be undone.';
            }
            
            if (typeof showConfirmation === 'function') {
                showConfirmation({
                    title: departmentsCount > 0 ? 'Cannot Delete Sector' : 'Confirm Delete',
                    message: message,
                    confirmText: departmentsCount > 0 ? 'OK' : 'Delete',
                    onConfirm: function() {
                        if (departmentsCount === 0) {
                            form.submit();
                        }
                    }
                });
            } else {
                if (departmentsCount === 0 && confirm(message)) {
                    form.submit();
                } else if (departmentsCount > 0) {
                    alert(message);
                }
            }
        }
    });
});
</script>

</x-app-layout>
