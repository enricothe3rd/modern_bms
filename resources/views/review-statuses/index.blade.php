@php
use Illuminate\Support\Str;
@endphp

<x-app-layout>
    @section('title', 'Review Statuses')

<x-dashboard-header
    title="Review Statuses"
    subtitle="Manage workflow review statuses for obligation requests."
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">Review Statuses</h2>
            <p class="text-gray-600 mt-1">Manage workflow statuses like Draft, Submitted, Approved, etc.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('management.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                ← Back to Management
            </a>
            @if(auth()->user()->canManageDepartments())
                <button id="addStatusBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                    + Add Status
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

    <!-- Review Statuses Table -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6">
        <table id="statusTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviewStatuses as $status)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $status->order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background-color: {{ $status->color }}20;">
                                        <svg class="h-5 w-5" style="color: {{ $status->color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $status->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                {{ $status->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $status->color }};"></div>
                                <span class="text-xs text-gray-600 font-mono">{{ $status->color }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $status->description }}">
                                {{ $status->description ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $status->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if(auth()->user()->canManageDepartments())
                                    <button class="editBtn text-indigo-600 hover:text-indigo-700 p-1 rounded-full hover:bg-indigo-50 transition duration-150 ease-in-out"
                                        data-id="{{ $status->id }}"
                                        data-name="{{ $status->name }}"
                                        data-code="{{ $status->code }}"
                                        data-description="{{ $status->description }}"
                                        data-color="{{ $status->color }}"
                                        data-order="{{ $status->order }}"
                                        data-is-active="{{ $status->is_active }}"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                @endif

                                @if(auth()->user()->canDeleteDepartments())
                                    <form action="{{ route('review-statuses.destroy', $status->id) }}" method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out delete-btn"
                                            data-status-name="{{ $status->name }}"
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
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No review statuses found</p>
                                <p class="text-sm">Get started by creating your first status.</p>
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
    const table = $('#statusTable').DataTable({
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
            searchPlaceholder: "Search statuses...",
            paginate: {
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            }
        },
        order: [[0, 'asc']],
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
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add Review Status</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('review-statuses.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label value="Status Name" />
                <x-input type="text" name="name" id="nameInput" placeholder="e.g., Submitted" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <x-input-label value="Description (Optional)" />
                <textarea name="description" id="descriptionInput" rows="2" placeholder="Brief description of this status" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <!-- Color -->
            <div class="mb-4">
                <x-input-label value="Color" />
                <div class="flex items-center space-x-2 mt-1">
                    <input type="color" name="color" id="colorInput" value="#3B82F6" class="h-10 w-20 border border-gray-300 rounded cursor-pointer" />
                    <input type="text" id="colorHex" value="#3B82F6" readonly class="flex-1 border-gray-300 rounded-lg shadow-sm bg-gray-50 text-sm font-mono" />
                </div>
                <x-input-error :messages="$errors->get('color')" class="mt-1" />
            </div>

            <!-- Order -->
            <div class="mb-4">
                <x-input-label value="Order" />
                <x-input type="number" name="order" id="orderInput" value="1" min="1" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('order')" class="mt-1" />
            </div>

            <!-- Is Active -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="isActiveInput" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
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
    id="deleteStatusModal"
    title="Confirm Delete"
    message="Are you sure you want to delete this review status? This action cannot be undone."
    confirmText="Delete"
    cancelText="Cancel"
    confirmClass="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('statusModal');
    const addBtn = document.getElementById('addStatusBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const nameInput = document.getElementById('nameInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const colorInput = document.getElementById('colorInput');
    const colorHex = document.getElementById('colorHex');
    const orderInput = document.getElementById('orderInput');
    const isActiveInput = document.getElementById('isActiveInput');

    // Sync color picker with hex input
    colorInput.addEventListener('input', function() {
        colorHex.value = this.value.toUpperCase();
    });

    // Show modal if there are validation errors
    @if ($errors->any())
        modal.classList.remove('hidden');
    @endif

    // Show modal if redirected from edit route
    @if(session('editStatus'))
        const editStatus = @json(session('editStatus'));
        modal.classList.remove('hidden');
        formTitle.textContent = 'Edit Review Status';
        submitBtn.textContent = 'Update';
        formElement.action = `/review-statuses/${editStatus.id}`;
        nameInput.value = editStatus.name;
        descriptionInput.value = editStatus.description || '';
        colorInput.value = editStatus.color;
        colorHex.value = editStatus.color;
        orderInput.value = editStatus.order;
        isActiveInput.checked = editStatus.is_active;
        
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
            formTitle.textContent = 'Add Review Status';
            submitBtn.textContent = 'Add';
            formElement.action = "{{ route('review-statuses.store') }}";
            nameInput.value = '';
            descriptionInput.value = '';
            colorInput.value = '#3B82F6';
            colorHex.value = '#3B82F6';
            orderInput.value = '1';
            isActiveInput.checked = true;

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

    // Edit buttons - Use event delegation
    document.body.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.editBtn');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            modal.classList.remove('hidden');
            formTitle.textContent = 'Edit Review Status';
            submitBtn.textContent = 'Update';
            formElement.action = `/review-statuses/${editBtn.dataset.id}`;
            nameInput.value = editBtn.dataset.name;
            descriptionInput.value = editBtn.dataset.description || '';
            colorInput.value = editBtn.dataset.color;
            colorHex.value = editBtn.dataset.color;
            orderInput.value = editBtn.dataset.order;
            isActiveInput.checked = editBtn.dataset.isActive === '1';

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
            
            const statusName = deleteBtn.getAttribute('data-status-name');
            const form = deleteBtn.closest('.delete-form');
            
            const message = `Are you sure you want to delete "${statusName}"? This action cannot be undone.`;
            
            if (typeof showConfirmation === 'function') {
                showConfirmation({
                    title: 'Confirm Delete',
                    message: message,
                    confirmText: 'Delete',
                    onConfirm: function() {
                        form.submit();
                    }
                });
            } else {
                if (confirm(message)) {
                    form.submit();
                }
            }
        }
    });
});
</script>

</x-app-layout>
