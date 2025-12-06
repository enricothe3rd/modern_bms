<x-app-layout>
    @section('title', 'Forms')

<x-dashboard-header
    title="Forms"
    subtitle="Manage form templates for signatory assignments."
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900">Forms</h2>
        <button id="addFormBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Add Form
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <p class="bg-green-50 text-green-900 px-4 py-2 rounded mb-4">{{ session('success') }}</p>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <p class="bg-red-50 text-red-900 px-4 py-2 rounded mb-4">{{ session('error') }}</p>
    @endif

<div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
    <div class="overflow-x-auto">
        <table id="formsTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tl-lg">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Usage</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($forms as $form)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $form->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $form->description ?? 'No description' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($form->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                            {{ $form->formSignatories->count() }} assignments
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center gap-2">
                                {{-- Edit Button --}}
                                <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                    data-id="{{ $form->id }}"
                                    data-name="{{ $form->name }}"
                                    data-description="{{ $form->description }}"
                                    data-is-active="{{ $form->is_active ? 1 : 0 }}"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                {{-- Delete Button (Only for users with delete permission) --}}
                                @if(auth()->user()->canDeleteForms())
                                    <form action="{{ route('forms.destroy', $form->id) }}" method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out delete-btn"
                                            data-form-name="{{ $form->name }}"
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
                    {{-- Hidden row for DataTables structure --}}
                    <tr style="display: none;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#formsTable').DataTable({
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
            searchPlaceholder: "Search forms...",
            paginate: {
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            },
            emptyTable: "No forms found. Click 'Add Form' to get started.",
            zeroRecords: "No matching forms found."
        },

        order: [[0, 'asc']],
        autoWidth: false,
        processing: false,
        serverSide: false
    });

    // Style the search input
    $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 transition duration-150 ease-in-out').removeClass('form-control');

    // Button container styling
    $('.dt-buttons').addClass('flex flex-wrap gap-2');

    // Add Tailwind classes to pagination and info
    $('.dataTables_info').addClass('text-sm text-gray-600');
    $('.dataTables_paginate').addClass('flex gap-1');

    // Style the pagination buttons
    $('.paginate_button').addClass('px-3 py-1 text-sm border rounded-lg hover:bg-indigo-50 text-indigo-600 border-indigo-200 transition duration-150 ease-in-out');
    $('.paginate_button.current').addClass('bg-indigo-600 text-white hover:bg-indigo-700').removeClass('bg-indigo-50 text-indigo-600');
    $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed text-gray-400 border-gray-200').removeClass('text-indigo-600 border-indigo-200');
});
</script>
@endpush

<!-- Modal Overlay -->
<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add Form</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('forms.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label value="Form Name" />
                <x-input type="text" name="name" id="nameInput" placeholder="e.g., Budget Request Form" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <x-input-label value="Description" />
                <textarea name="description" id="descriptionInput" placeholder="Brief description of the form" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="isActiveInput" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300">Cancel</button>
                <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">Save</button>
            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($errors->any())
            const modal = document.getElementById('formModal');
            modal.classList.remove('hidden');
        @endif
    });

    const addBtn = document.getElementById('addFormBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('formModal');

    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');

    const nameInput = document.getElementById('nameInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const isActiveInput = document.getElementById('isActiveInput');

    // Show modal for Add
    addBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        formTitle.textContent = 'Add Form';
        submitBtn.textContent = 'Save';
        formElement.action = "{{ route('forms.store') }}";

        resetForm();

        // Remove old PUT method if exists
        const putMethod = formElement.querySelector('input[name="_method"]');
        if (putMethod) putMethod.remove();
    });

    // Cancel / Close modal
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));

    function resetForm() {
        nameInput.value = '';
        descriptionInput.value = '';
        isActiveInput.checked = true;
    }

    // Edit buttons
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.remove('hidden');
            formTitle.textContent = 'Edit Form';
            submitBtn.textContent = 'Update';
            formElement.action = `/forms/${button.dataset.id}`;

            nameInput.value = button.dataset.name;
            descriptionInput.value = button.dataset.description || '';
            isActiveInput.checked = button.dataset.isActive === '1';

            // Add PUT method if not already
            if (!formElement.querySelector('input[name="_method"]')) {
                const putMethod = document.createElement('input');
                putMethod.type = 'hidden';
                putMethod.name = '_method';
                putMethod.value = 'PUT';
                formElement.appendChild(putMethod);
            }
        });
    });
</script>

</x-app-layout>