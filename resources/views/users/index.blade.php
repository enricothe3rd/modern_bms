<x-app-layout>
    @section('title', 'Users')

<x-dashboard-header
    title="Users"
    subtitle="Manage system users and their assignments."
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900">System Users</h2>
        <button id="addUserBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Add User
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <p class="bg-green-50 text-green-900 px-4 py-2 rounded mb-4">{{ session('success') }}</p>
    @endif

<div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
    <div class="overflow-x-auto">
        {{-- DataTables will initialize on this table --}}
        <table id="userTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tl-lg">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                            @if($user->role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if($user->role->name === 'Admin') bg-red-100 text-red-800
                                    @elseif($user->role->name === 'Manager') bg-blue-100 text-blue-800
                                    @elseif($user->role->name === 'Budget Officer Head') bg-purple-100 text-purple-800
                                    @elseif($user->role->name === 'Budget Officer') bg-indigo-100 text-indigo-800
                                    @elseif($user->role->name === 'User') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $user->role->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                            @if($user->department)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $user->department->code }} - {{ $user->department->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center gap-2">
                                {{-- Edit Button with Icon --}}
                                <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-role-id="{{ $user->role_id ?? '' }}"
                                    data-department-id="{{ $user->department_id ?? '' }}"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                {{-- Reset Password Button with Icon --}}
                                <form action="{{ route('users.reset-password', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="text-blue-600 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50 transition duration-150 ease-in-out"
                                        onclick="return confirm('Are you sure you want to reset {{ $user->name }}\'s password to default (123456)?')"
                                        title="Reset Password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    </button>
                                </form>

                                {{-- Delete Form with Icon --}}
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out"
                                        onclick="return confirm('Are you sure you want to delete {{ $user->name }}?')"
                                        title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        // Define the custom layout with improved flex for alignment and spacing
        dom: "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'<'buttons'B><'search-wrapper'f>>" +
             "rt" + // Table
             "<'flex flex-col sm:flex-row justify-between items-center mt-4 gap-4'<'info'i><'pagination'p>>",

        // Define buttons with clean, modern Tailwind classes
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
            { targets: -1, orderable: false, searchable: false } // Disable sorting and searching on Actions column
        ],
        language: {
            search: "", // Remove default 'Search:' text
            searchPlaceholder: "Search users...",
            paginate: { // Better pagination labels
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            }
        },

        // Order by name by default
        order: [[0, 'asc']]
    });

    // Style the search input, info, and pagination elements for a modern look

    // Search input styling (important for the modern look)
    $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 transition duration-150 ease-in-out').removeClass('form-control');

    // Button container styling (to ensure buttons are grouped nicely)
    $('.dt-buttons').addClass('flex flex-wrap gap-2');

    // Add Tailwind classes to pagination and info for proper spacing/font
    $('.dataTables_info').addClass('text-sm text-gray-600');
    $('.dataTables_paginate').addClass('flex gap-1');

    // Style the pagination buttons (prev/next)
    $('.paginate_button').addClass('px-3 py-1 text-sm border rounded-lg hover:bg-indigo-50 text-indigo-600 border-indigo-200 transition duration-150 ease-in-out');
    $('.paginate_button.current').addClass('bg-indigo-600 text-white hover:bg-indigo-700').removeClass('bg-indigo-50 text-indigo-600');
    $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed text-gray-400 border-gray-200').removeClass('text-indigo-600 border-indigo-200');
});
</script>
@endpush

<!-- Modal Overlay -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add User</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('users.store') }}">
            @csrf

            <!-- Name Row -->
            <div class="mb-4">
                <x-input-label value="Full Name" />
                <x-input type="text" name="name" id="nameInput" placeholder="Enter full name" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Email Row -->
            <div class="mb-4">
                <x-input-label value="Email Address" />
                <x-input type="email" name="email" id="emailInput" placeholder="Enter email address" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password Row -->
            <div class="mb-4" id="passwordSection">
                <x-input-label value="Password (Default: 123456)" />
                <x-input type="password" name="password" id="passwordInput" placeholder="Leave empty to use default (123456)" class="mt-1 block w-full" />
                <p class="text-xs text-gray-500 mt-1">Leave empty to use default password: 123456</p>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password Row -->
            <div class="mb-4" id="confirmPasswordSection">
                <x-input-label value="Confirm Password" />
                <x-input type="password" name="password_confirmation" id="confirmPasswordInput" placeholder="Confirm password (if provided above)" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <!-- Role Row -->
            <div class="mb-4">
                <x-input-label value="Role" />
                <select name="role_id" id="roleInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Role (Optional)</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role_id')" class="mt-1" />
            </div>

            <!-- Department Row -->
            <div class="mb-4">
                <x-input-label value="Department" />
                <select name="department_id" id="departmentInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Department (Optional)</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $user->department_id ?? '') == $department->id ? 'selected' : '' }}>
                            {{ $department->code }} - {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300">Cancel</button>
                <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">Add</button>
            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($errors->any())
            const modal = document.getElementById('userModal');
            modal.classList.remove('hidden');
        @endif
    });

    const addBtn = document.getElementById('addUserBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('userModal');

    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');

    const nameInput = document.getElementById('nameInput');
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    const confirmPasswordInput = document.getElementById('confirmPasswordInput');
    const roleInput = document.getElementById('roleInput');
    const departmentInput = document.getElementById('departmentInput');

    const passwordSection = document.getElementById('passwordSection');
    const confirmPasswordSection = document.getElementById('confirmPasswordSection');

    // Show modal for Add
    addBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        formTitle.textContent = 'Add User';
        submitBtn.textContent = 'Add';
        formElement.action = "{{ route('users.store') }}";

        // Show password fields for new user (but make them optional)
        passwordSection.style.display = 'block';
        confirmPasswordSection.style.display = 'block';
        passwordInput.required = false; // Password is optional, will use default if empty

        nameInput.value = '';
        emailInput.value = '';
        passwordInput.value = ''; // Leave empty to use default
        confirmPasswordInput.value = '';
        roleInput.value = '';
        departmentInput.value = '';

        // Remove old PUT method if exists
        const putMethod = formElement.querySelector('input[name="_method"]');
        if (putMethod) putMethod.remove();
    });

    // Cancel / Close modal
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));

    // Edit buttons
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.remove('hidden');
            formTitle.textContent = 'Edit User';
            submitBtn.textContent = 'Update';
            formElement.action = `/users/${button.dataset.id}`;

            // Hide password fields for edit (optional update)
            passwordSection.style.display = 'none';
            confirmPasswordSection.style.display = 'none';
            passwordInput.required = false;

            nameInput.value = button.dataset.name;
            emailInput.value = button.dataset.email;
            roleInput.value = button.dataset.roleId;
            departmentInput.value = button.dataset.departmentId;

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