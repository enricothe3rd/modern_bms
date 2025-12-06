<x-app-layout>
    @section('title', 'Accounts')

<x-dashboard-header
    title="Accounts"
    subtitle="Manage your chart of accounts."
/>

<div class="max-w-6xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900">Chart of Accounts</h2>
        <button id="addAccountBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Add Account
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <p class="bg-green-50 text-green-900 px-4 py-2 rounded mb-4">{{ session('success') }}</p>
    @endif

<div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
    <div class="overflow-x-auto">
        {{-- DataTables will initialize on this table --}}
        <table id="accountTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tl-lg">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider">Sub Accounts</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($accounts as $account)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $account->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $account->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                            @if($account->subAccounts->count() > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $account->subAccounts->count() }} sub-accounts
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center gap-2">
                                {{-- Edit Button with Icon --}}
                                <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                    data-id="{{ $account->id }}"
                                    data-code="{{ $account->code }}"
                                    data-description="{{ $account->description }}"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                {{-- Delete Form with Icon (Only for users with delete permission) --}}
                                @if(auth()->user()->canDeleteAccounts())
                                    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out delete-btn"
                                            data-account-name="{{ $account->description }}"
                                            title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
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
    $('#accountTable').DataTable({
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
            searchPlaceholder: "Search accounts...",
            paginate: { // Better pagination labels
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            }
        },

        // Order by code by default
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
<div id="accountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add Account</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('accounts.store') }}">
            @csrf

            <!-- Code Row -->
            <div class="mb-4">
                <x-input-label value="Account Code" />
                <x-input type="text" name="code" id="codeInput" placeholder="e.g., 1000, 1100" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('code')" class="mt-1" />
            </div>

            <!-- Description Row -->
            <div class="mb-4">
                <x-input-label value="Description" />
                <x-input type="text" name="description" id="descriptionInput" placeholder="Account description" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
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
            const modal = document.getElementById('accountModal');
            modal.classList.remove('hidden');
        @endif
    });

    const addBtn = document.getElementById('addAccountBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('accountModal');

    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');

    const codeInput = document.getElementById('codeInput');
    const descriptionInput = document.getElementById('descriptionInput');

    // Show modal for Add
    addBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        formTitle.textContent = 'Add Account';
        submitBtn.textContent = 'Add';
        formElement.action = "{{ route('accounts.store') }}";

        codeInput.value = '';
        descriptionInput.value = '';

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
            formTitle.textContent = 'Edit Account';
            submitBtn.textContent = 'Update';
            formElement.action = `/accounts/${button.dataset.id}`;

            codeInput.value = button.dataset.code;
            descriptionInput.value = button.dataset.description;

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

<!-- Delete Confirmation Modal -->
<x-confirmation-modal 
    id="deleteAccountModal"
    title="Confirm Delete"
    message="Are you sure you want to delete this account? This action cannot be undone."
    confirmText="Delete"
    cancelText="Cancel"
    confirmClass="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const accountName = this.getAttribute('data-account-name');
            const form = this.closest('.delete-form');
            
            showConfirmation({
                title: 'Confirm Delete',
                message: `Are you sure you want to delete "${accountName}"? This action cannot be undone.`,
                onConfirm: function() {
                    form.submit();
                }
            });
        });
    });
});
</script>

</x-app-layout>