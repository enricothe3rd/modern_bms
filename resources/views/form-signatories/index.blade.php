<x-app-layout>
    @section('title', 'Form Signatories')

<x-dashboard-header
    title="Form Signatories"
    subtitle="Manage form signatory assignments by department."
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900">Form Signatories</h2>
        <button id="addFormSignatoryBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Assign Signatories
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <p class="bg-green-50 text-green-900 px-4 py-2 rounded mb-4">{{ session('success') }}</p>
    @endif

<div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
    <div class="overflow-x-auto">
        {{-- DataTables will initialize on this table --}}
        <table id="formSignatoryTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tl-lg">Form Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Signatories</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @php
                    $hasData = false;
                @endphp
                @if(isset($formSignatories) && $formSignatories->count() > 0)
                    @foreach($formSignatories as $formName => $departmentGroups)
                        @foreach($departmentGroups as $departmentId => $signatories)
                            @if($signatories->isNotEmpty())
                                @php
                                    $firstSignatory = $signatories->first();
                                    $hasData = true;
                                @endphp
                                @if($firstSignatory && $firstSignatory->department && $firstSignatory->signatory)
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                {{ $formName }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $firstSignatory->department->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($signatories->sortBy('order') as $signatory)
                                                    @if($signatory && $signatory->signatory)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                            {{ $signatory->order }}. {{ $signatory->signatory->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center items-center gap-2">
                                                {{-- Edit Button with Icon --}}
                                                <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                                    data-id="{{ $firstSignatory->id }}"
                                                    data-form-id="{{ $firstSignatory->form_id }}"
                                                    data-department-id="{{ $departmentId }}"
                                                    data-signatories="{{ $signatories->pluck('signatory_id')->toJson() }}"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>

                                                {{-- Delete Form with Icon (Only for users with delete permission) --}}
                                                @if(auth()->user()->canDeleteFormSignatories())
                                                    <form action="{{ route('form-signatories.destroy', $firstSignatory->id) }}" method="POST" class="inline-block delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out delete-btn"
                                                            data-form-name="{{ $formName }}"
                                                            data-department-name="{{ $firstSignatory->department->name }}"
                                                            title="Delete">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    @endforeach
                @endif
                
                @if(!$hasData)
                    {{-- Empty row for DataTables when no data exists --}}
                    <tr style="display: none;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    /* Basic styling for form elements */
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready - initializing form signatories');
    
    $('#formSignatoryTable').DataTable({
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
            searchPlaceholder: "Search form signatories...",
            paginate: {
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            },
            emptyTable: "No form signatories found. Click 'Assign Signatories' to get started.",
            zeroRecords: "No matching form signatories found."
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

    // Initialize modal functionality with native search
    initializeModalFunctionality();
    

});

// Simple modal functionality
function initializeModalSelects() {
    console.log('Modal selects ready');
}

function initializeModalFunctionality() {
    const addBtn = $('#addFormSignatoryBtn');
    const cancelBtn = $('#cancelBtn');
    const closeModal = $('#closeModal');
    const modal = $('#formSignatoryModal');
    const formElement = $('#formElement');
    const formTitle = $('#formTitle');
    const submitBtn = $('#submitBtn');
    const formSelect = $('#formSelect');
    const departmentSelect = $('#departmentSelect');
    const signatoriesContainer = $('#signatoriesContainer');
    const addSignatoryBtn = $('#addSignatoryBtn');

    let signatoryCount = 1;

    // Pre-generate user options for JavaScript
    const userOptions = [
        '<option value="">Select Signatory</option>',
        @if(isset($users) && $users instanceof \Illuminate\Support\Collection && $users->count() > 0)
            @foreach($users as $user)
                '<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'No Role' }})</option>',
            @endforeach
        @endif
    ].join('');

    function resetForm() {
        signatoriesContainer.html(`
            <div class="signatory-row flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700 w-8">1.</span>
                <select name="signatories[]" class="signatory-select flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    ${userOptions}
                </select>
                <button type="button" class="remove-signatory text-red-600 hover:text-red-800 p-1" style="display: none;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `);
        signatoryCount = 1;
        
        // Reset checkbox and department select
        $('#assignToAllDepartments').prop('checked', false);
        departmentSelect.prop('disabled', false).removeClass('bg-gray-100');
    }

    function handleAssignToAllDepartments() {
        const assignToAllCheckbox = $('#assignToAllDepartments');
        
        assignToAllCheckbox.on('change', function() {
            if ($(this).is(':checked')) {
                // Disable department select and clear its value
                departmentSelect.prop('disabled', true).addClass('bg-gray-100').val('');
            } else {
                // Enable department select
                departmentSelect.prop('disabled', false).removeClass('bg-gray-100');
            }
        });
    }

    function updateSignatoryNumbers() {
        signatoriesContainer.find('.signatory-row').each(function(index) {
            $(this).find('span').text(`${index + 1}.`);
        });
        signatoryCount = signatoriesContainer.find('.signatory-row').length;
    }

    function updateRemoveButtons() {
        const removeButtons = signatoriesContainer.find('.remove-signatory');
        if (signatoryCount > 1) {
            removeButtons.show();
        } else {
            removeButtons.hide();
        }
    }

    // Show modal for Add
    addBtn.on('click', function() {
        console.log('Add button clicked');
        
        modal.removeClass('hidden');
        formTitle.text('Assign Form Signatories');
        submitBtn.text('Assign');
        formElement.attr('action', "{{ route('form-signatories.store') }}");

        resetForm();
        formElement.find('input[name="_method"]').remove();
        
        // Initialize modal selects
        initializeModalSelects();
        
        // Initialize assign to all departments functionality
        handleAssignToAllDepartments();
    });

    // Cancel / Close modal
    cancelBtn.on('click', function() {
        modal.addClass('hidden');
    });
    
    closeModal.on('click', function() {
        modal.addClass('hidden');
    });

    // Add signatory functionality
    addSignatoryBtn.on('click', function() {
        signatoryCount++;
        const signatoryRow = $(`
            <div class="signatory-row flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700 w-8">${signatoryCount}.</span>
                <select name="signatories[]" class="signatory-select flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    ${userOptions}
                </select>
                <button type="button" class="remove-signatory text-red-600 hover:text-red-800 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `);
        
        signatoriesContainer.append(signatoryRow);
        
        // No special initialization needed for regular select
        
        updateRemoveButtons();
    });

    // Remove signatory functionality
    signatoriesContainer.on('click', '.remove-signatory', function() {
        const row = $(this).closest('.signatory-row');
        row.remove();
        updateSignatoryNumbers();
        updateRemoveButtons();
    });

    // Edit buttons
    $(document).on('click', '.editBtn', function() {
        const button = $(this);
        console.log('Edit button clicked');
        
        modal.removeClass('hidden');
        formTitle.text('Edit Form Signatories');
        submitBtn.text('Update');
        formElement.attr('action', `/form-signatories/${button.data('id')}`);

        const signatories = JSON.parse(button.attr('data-signatories'));
        console.log('Parsed signatories:', signatories);
        
        signatoriesContainer.empty();
        signatoryCount = 0;

        signatories.forEach((signatoryId, index) => {
            signatoryCount++;
            const signatoryRow = $(`
                <div class="signatory-row flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 w-8">${signatoryCount}.</span>
                    <select name="signatories[]" class="signatory-select flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        ${userOptions}
                    </select>
                    <button type="button" class="remove-signatory text-red-600 hover:text-red-800 p-1" ${signatoryCount === 1 ? 'style="display: none;"' : ''}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `);
            signatoriesContainer.append(signatoryRow);
        });

        // Set form and department values
        formSelect.val(button.data('form-id')).trigger('change');
        departmentSelect.val(button.data('department-id')).trigger('change');
        
        // Set signatory values
        signatoriesContainer.find('.signatory-select').each(function(index) {
            if (signatories[index]) {
                $(this).val(signatories[index]).trigger('change');
            }
        });
        
        // Initialize assign to all departments functionality for edit mode
        handleAssignToAllDepartments();

        updateRemoveButtons();

        if (formElement.find('input[name="_method"]').length === 0) {
            formElement.append('<input type="hidden" name="_method" value="PUT">');
        }
    });
}
</script>
@endpush

<!-- Modal Overlay -->
<div id="formSignatoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Assign Form Signatories</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('form-signatories.store') }}">
            @csrf

            <!-- Form Selection Row -->
            <div class="mb-4">
                <x-input-label value="Form" />
                <select name="form_id" id="formSelect" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Form</option>
                    @if(isset($forms) && $forms instanceof \Illuminate\Support\Collection && $forms->count() > 0)
                        @foreach($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->name }}</option>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('form_id')" class="mt-1" />
            </div>

            <!-- Department Row -->
            <div class="mb-4">
                <x-input-label value="Department" />
                <select name="department_id" id="departmentSelect" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Department</option>
                    @if(isset($departments) && $departments instanceof \Illuminate\Support\Collection && $departments->count() > 0)
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
                
                <!-- Assign to All Departments Option -->
                <div class="mt-3">
                    <label class="flex items-center">
                        <input type="checkbox" id="assignToAllDepartments" name="assign_to_all_departments" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Assign to all departments</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Check this to assign the same signatories to all departments for this form</p>
                </div>
            </div>

            <!-- Signatories Row -->
            <div class="mb-4">
                <x-input-label value="Signatories (in order of signature)" />
                <div id="signatoriesContainer" class="mt-2 space-y-2">
                    <div class="signatory-row flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700 w-8">1.</span>
                        <select name="signatories[]" class="signatory-select flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Signatory</option>
                            @if(isset($users) && $users instanceof \Illuminate\Support\Collection && $users->count() > 0)
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'No Role' }})</option>
                                @endforeach
                            @endif
                        </select>
                        <button type="button" class="remove-signatory text-red-600 hover:text-red-800 p-1" style="display: none;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" id="addSignatoryBtn" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                    + Add Another Signatory
                </button>
                <x-input-error :messages="$errors->get('signatories')" class="mt-1" />
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300">Cancel</button>
                <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">Assign</button>
            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($errors->any())
            const modal = document.getElementById('formSignatoryModal');
            modal.classList.remove('hidden');
        @endif
    });
</script>

</x-app-layout>