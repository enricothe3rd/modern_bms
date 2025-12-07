<x-app-layout>
    @section('title', 'User Department Assignments')

<x-dashboard-header
    title="User Department Assignments"
    subtitle="Assign users to work in different departments while keeping their role."
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900">User Department Assignments</h2>
        <button id="addUserDepartmentAssignmentBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Assign User to Departments
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <p class="bg-green-50 text-green-900 px-4 py-2 rounded mb-4">{{ session('success') }}</p>
    @endif

    <!-- Error Message -->
    @if($errors->has('error'))
        <p class="bg-red-50 text-red-900 px-4 py-2 rounded mb-4">{{ $errors->first('error') }}</p>
    @endif

<div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
    <div class="overflow-x-auto">
        <table id="userDepartmentAssignmentTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tl-lg">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Assigned Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Review Statuses</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @php
                    $hasData = false;
                @endphp
                @if(isset($userDepartmentAssignments) && $userDepartmentAssignments->count() > 0)
                    @foreach($userDepartmentAssignments as $userName => $assignments)
                        @foreach($assignments as $assignment)
                            @php
                                $hasData = true;
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-indigo-700">
                                                    {{ strtoupper(substr($assignment->user->name ?? 'U', 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $assignment->user->name ?? 'Unknown' }}</div>
                                            <div class="text-sm text-gray-500">{{ $assignment->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                        {{ $assignment->user->role->name ?? 'No Role' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        {{ $assignment->department->code ?? 'UNK' }} - {{ $assignment->department->name ?? 'Unknown Department' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if($assignment->reviewStatuses && $assignment->reviewStatuses->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($assignment->reviewStatuses as $status)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $status->color }}20; color: {{ $status->color }};">
                                                    <span class="inline-block w-2 h-2 rounded-full mr-1" style="background-color: {{ $status->color }};"></span>
                                                    {{ $status->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">No statuses assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if($assignment->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if($assignment->start_date || $assignment->end_date)
                                        <div class="text-xs">
                                            @if($assignment->start_date)
                                                <div>From: {{ $assignment->start_date->format('M d, Y') }}</div>
                                            @endif
                                            @if($assignment->end_date)
                                                <div>To: {{ $assignment->end_date->format('M d, Y') }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">No dates set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-2">
                                        <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                            data-id="{{ $assignment->id }}"
                                            data-user-id="{{ $assignment->user_id }}"
                                            data-department-id="{{ $assignment->department_id }}"
                                            data-is-active="{{ $assignment->is_active ? 1 : 0 }}"
                                            data-start-date="{{ $assignment->start_date ? $assignment->start_date->format('Y-m-d') : '' }}"
                                            data-end-date="{{ $assignment->end_date ? $assignment->end_date->format('Y-m-d') : '' }}"
                                            data-notes="{{ $assignment->notes }}"
                                            data-status-ids="{{ $assignment->reviewStatuses->pluck('id')->implode(',') }}"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <form action="{{ route('user-department-assignments.destroy', $assignment->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out"
                                                onclick="return confirm('Are you sure you want to delete this department assignment?')"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
                
                @if(!$hasData)
                    <tr style="display: none;">
                        <td></td>
                        <td></td>
                        <td></td>
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

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready - initializing user department assignments');
    
    $('#userDepartmentAssignmentTable').DataTable({
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
            searchPlaceholder: "Search user department assignments...",
            paginate: {
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            },
            emptyTable: "No user department assignments found. Click 'Assign User to Department' to get started.",
            zeroRecords: "No matching user department assignments found."
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

    // Initialize modal functionality
    initializeModalFunctionality();
});

function initializeModalFunctionality() {
    const addBtn = $('#addUserDepartmentAssignmentBtn');
    const cancelBtn = $('#cancelBtn');
    const closeModal = $('#closeModal');
    const modal = $('#userDepartmentAssignmentModal');
    const formElement = $('#formElement');
    const formTitle = $('#formTitle');
    const submitBtn = $('#submitBtn');

    function resetForm() {
        formElement[0].reset();
        $('#isActive').prop('checked', true);
        // Uncheck all department checkboxes
        $('input[name="department_ids[]"]').prop('checked', false);
    }

    // Show modal for Add
    addBtn.on('click', function() {
        console.log('Add button clicked');
        
        modal.removeClass('hidden');
        formTitle.text('Assign User to Departments');
        submitBtn.text('Assign');
        formElement.attr('action', "{{ route('user-department-assignments.store') }}");

        resetForm();
        formElement.find('input[name="_method"]').remove();
    });

    // Cancel / Close modal
    cancelBtn.on('click', function() {
        modal.addClass('hidden');
    });
    
    closeModal.on('click', function() {
        modal.addClass('hidden');
    });

    // Edit buttons
    $(document).on('click', '.editBtn', function() {
        const button = $(this);
        console.log('Edit button clicked');
        
        modal.removeClass('hidden');
        formTitle.text('Edit Department Assignment');
        submitBtn.text('Update');
        formElement.attr('action', `/user-department-assignments/${button.data('id')}`);

        // Set form values
        $('#userSelect').val(button.data('user-id'));
        
        // For edit mode, we'll only check the current department
        // Reset all checkboxes first
        $('input[name="department_ids[]"]').prop('checked', false);
        // Check the current department
        $(`input[name="department_ids[]"][value="${button.data('department-id')}"]`).prop('checked', true);
        
        // Reset and set review status checkboxes
        $('input[name="review_status_ids[]"]').prop('checked', false);
        const statusIds = button.data('status-ids') ? button.data('status-ids').toString().split(',') : [];
        statusIds.forEach(function(statusId) {
            if (statusId) {
                $(`input[name="review_status_ids[]"][value="${statusId}"]`).prop('checked', true);
            }
        });
        
        $('#isActive').prop('checked', button.data('is-active') == 1);
        $('#startDate').val(button.data('start-date'));
        $('#endDate').val(button.data('end-date'));
        $('#notes').val(button.data('notes'));

        if (formElement.find('input[name="_method"]').length === 0) {
            formElement.append('<input type="hidden" name="_method" value="PUT">');
        }
    });

    // Select All / Clear All functionality
    $('#selectAllDepts').on('click', function() {
        $('input[name="department_ids[]"]').prop('checked', true);
    });

    $('#clearAllDepts').on('click', function() {
        $('input[name="department_ids[]"]').prop('checked', false);
    });

    // Select All / Clear All for Review Statuses
    $('#selectAllStatuses').on('click', function() {
        $('input[name="review_status_ids[]"]').prop('checked', true);
    });

    $('#clearAllStatuses').on('click', function() {
        $('input[name="review_status_ids[]"]').prop('checked', false);
    });
}
</script>
@endpush

<!-- Modal Overlay -->
<div id="userDepartmentAssignmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Assign User to Departments</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('user-department-assignments.store') }}">
            @csrf

            <!-- User Selection -->
            <div class="mb-4">
                <x-input-label value="User" />
                <select name="user_id" id="userSelect" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select User</option>
                    @if(isset($users) && $users instanceof \Illuminate\Support\Collection && $users->count() > 0)
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'No Role' }}) - {{ $user->email }}</option>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
            </div>

            <!-- Department Selection -->
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <x-input-label value="Departments to Assign" />
                    <div class="text-xs space-x-2">
                        <button type="button" id="selectAllDepts" class="text-indigo-600 hover:text-indigo-800">Select All</button>
                        <span class="text-gray-400">|</span>
                        <button type="button" id="clearAllDepts" class="text-indigo-600 hover:text-indigo-800">Clear All</button>
                    </div>
                </div>
                <div class="mt-1 border border-gray-300 rounded-md p-3 max-h-48 overflow-y-auto bg-gray-50">
                    @if(isset($departments) && $departments instanceof \Illuminate\Support\Collection && $departments->count() > 0)
                        @foreach($departments as $department)
                            <label class="flex items-center mb-2 p-2 rounded hover:bg-white transition-colors cursor-pointer">
                                <input type="checkbox" name="department_ids[]" value="{{ $department->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-3 text-sm text-gray-700 font-medium">
                                    <span class="font-bold text-indigo-600">{{ $department->code }}</span> - {{ $department->name }}
                                </span>
                            </label>
                        @endforeach
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Select one or more departments to assign the user to</p>
                <x-input-error :messages="$errors->get('department_ids')" class="mt-1" />
                <x-input-error :messages="$errors->get('department_ids.*')" class="mt-1" />
            </div>

            <!-- Review Status Selection -->
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <x-input-label value="Review Statuses (Approval Workflow)" />
                    <div class="text-xs space-x-2">
                        <button type="button" id="selectAllStatuses" class="text-purple-600 hover:text-purple-800">Select All</button>
                        <span class="text-gray-400">|</span>
                        <button type="button" id="clearAllStatuses" class="text-purple-600 hover:text-purple-800">Clear All</button>
                    </div>
                </div>
                <div class="mt-1 border border-gray-300 rounded-md p-3 max-h-64 overflow-y-auto bg-gray-50">
                    @if(isset($reviewStatuses) && $reviewStatuses instanceof \Illuminate\Support\Collection && $reviewStatuses->count() > 0)
                        @foreach($reviewStatuses as $status)
                            <label class="flex items-center mb-2 p-2 rounded hover:bg-white transition-colors cursor-pointer">
                                <input type="checkbox" name="review_status_ids[]" value="{{ $status->id }}" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <span class="ml-3 flex items-center text-sm text-gray-700 font-medium flex-1">
                                    <span class="inline-block w-4 h-4 rounded-full mr-2" style="background-color: {{ $status->color }};"></span>
                                    <span class="font-bold">{{ $status->name }}</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ $status->description }})</span>
                                </span>
                            </label>
                        @endforeach
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <strong>Select which approval stages this user can handle.</strong><br>
                    Example: Budget Staff can handle "Budget Staff Review" and "Budget Staff Approved"
                </p>
                <x-input-error :messages="$errors->get('review_status_ids')" class="mt-1" />
                <x-input-error :messages="$errors->get('review_status_ids.*')" class="mt-1" />
            </div>

            <!-- Active Status -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" id="isActive" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Active Assignment</span>
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-1" />
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label value="Start Date (Optional)" />
                    <input type="date" name="start_date" id="startDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="End Date (Optional)" />
                    <input type="date" name="end_date" id="endDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <x-input-label value="Notes (Optional)" />
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Additional notes about this department assignment..."></textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
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
            const modal = document.getElementById('userDepartmentAssignmentModal');
            modal.classList.remove('hidden');
        @endif
    });
</script>

</x-app-layout>