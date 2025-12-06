<x-app-layout>
    @section('title', 'Budget Allocations')

<x-dashboard-header
    title="Budget Allocations"
    subtitle="Manage budget allocations for {{ $department->name }} - {{ $expenseType->description }}"
/>

<div class="max-w-7xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">Budget Allocations</h2>
            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                <span><strong>Department:</strong> {{ $department->name }} ({{ $department->code }})</span>
                <span>•</span>
                <span><strong>Expense Type:</strong> {{ $expenseType->description }} ({{ $expenseType->acronym }})</span>
                <span>•</span>
                <span><strong>Sector:</strong> {{ $department->sector->name ?? 'No Sector' }}</span>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('department-expense-types.index', $department->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                ← Back to Expense Types
            </a>
            <button id="addAllocationBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                Add Budget Allocation
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <p class="bg-green-50 text-green-900 px-4 py-2 rounded mb-4">{{ session('success') }}</p>
    @endif

    <!-- Warning Message -->
    @if(session('warning'))
        <p class="bg-yellow-50 text-yellow-900 px-4 py-2 rounded mb-4">{{ session('warning') }}</p>
    @endif

<div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-900">Budget Allocations</h3>
            <div>
                <label for="yearFilter" class="text-sm font-medium text-gray-700 mr-2">Year:</label>
                <select id="yearFilter" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                    <option value="">All Years</option>
                    @php
                        $currentYear = date('Y');
                        $startYear = 2020;
                    @endphp
                    @for($year = $currentYear + 1; $year >= $startYear; $year--)
                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>
        @if($allocations->count() > 0)
            <div class="text-right">
                <div class="text-2xl font-bold text-green-600" id="totalAmount">₱{{ number_format($allocations->sum('amount'), 2) }}</div>
                <div class="text-sm text-gray-500">Total Allocated</div>
            </div>
        @endif
    </div>
    
    <div class="overflow-x-auto">
        {{-- DataTables will initialize on this table --}}
        <table id="allocationTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tl-lg">Year</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Account Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-700 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-indigo-700 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-indigo-700 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($allocations as $allocation)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out" data-year="{{ $allocation->year }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-semibold bg-gray-100 text-gray-800">
                                {{ $allocation->year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $allocation->account_type === 'account' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} uppercase">
                                {{ $allocation->account_type === 'account' ? 'Account' : 'Sub Account' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $allocation->account ? $allocation->account->code : $allocation->subAccount->code }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $allocation->account ? $allocation->account->description : $allocation->subAccount->description }}
                            </div>
                            @if($allocation->subAccount && $allocation->subAccount->account)
                                <div class="text-xs text-blue-600 mt-1">
                                    Parent: {{ $allocation->subAccount->account->code }} - {{ $allocation->subAccount->account->description }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $allocation->description ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-lg font-semibold text-gray-900">₱{{ number_format($allocation->amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center gap-2">
                                {{-- Edit Button with Icon --}}
                                <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                    data-id="{{ $allocation->id }}"
                                    data-year="{{ $allocation->year }}"
                                    data-account-type="{{ $allocation->account_type }}"
                                    data-account-id="{{ $allocation->account_id }}"
                                    data-sub-account-id="{{ $allocation->sub_account_id }}"
                                    data-amount="{{ $allocation->amount }}"
                                    data-description="{{ $allocation->description }}"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                {{-- Delete Form with Icon --}}
                                <form action="{{ route('department-expense-type-allocations.destroy', [$department->id, $expenseType->id, $allocation->id]) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition duration-150 ease-in-out"
                                        onclick="return confirm('Are you sure you want to delete this allocation?')"
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
    const table = $('#allocationTable').DataTable({
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
            searchPlaceholder: "Search budget allocations...",
            paginate: { // Better pagination labels
                next: 'Next &rarr;',
                previous: '&larr; Previous'
            }
        },

        // Order by year desc, then account type
        order: [[0, 'desc']]
    });

    // Function to update total amount based on visible rows
    function updateTotalAmount() {
        let total = 0;
        table.rows({ search: 'applied' }).every(function() {
            const row = this.node();
            const amountText = $(row).find('td:eq(4)').text().replace(/[₱,]/g, '');
            const amount = parseFloat(amountText) || 0;
            total += amount;
        });
        
        $('#totalAmount').text('₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    // Custom search function for year filtering
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            const selectedYear = $('#yearFilter').val();
            
            // If no year selected, show all
            if (!selectedYear) {
                return true;
            }
            
            // Get the year from the row's data attribute
            const row = table.row(dataIndex).node();
            const rowYear = $(row).data('year');
            
            // Compare years
            return rowYear == selectedYear;
        }
    );

    // Year filter functionality
    $('#yearFilter').on('change', function() {
        table.draw();
        updateTotalAmount();
    });

    // Apply initial filter after DataTables is fully initialized
    table.draw();
    updateTotalAmount();

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

</div>

<!-- Modal Overlay -->
<div id="allocationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative max-h-screen overflow-y-auto">

        <!-- Close Button -->
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="formTitle">Add Budget Allocation</h2>

        <!-- Form -->
        <form id="formElement" method="POST" action="{{ route('department-expense-type-allocations.store', [$department->id, $expenseType->id]) }}">
            @csrf

            <!-- Single Allocation Form -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Year -->
                <div>
                    <x-input-label value="Year" />
                    <select name="year" id="yearInput" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @php
                            $currentYear = date('Y');
                            $startYear = 2020;
                        @endphp
                        @for($year = $currentYear + 1; $year >= $startYear; $year--)
                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    <x-input-error :messages="$errors->get('year')" class="mt-1" />
                </div>

                <!-- Account/Sub-Account Selection -->
                <div>
                    <x-input-label value="Account / Sub Account" />
                    <select id="accountSubAccountInput" name="account_or_sub_account" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select Account or Sub Account</option>
                        @foreach($accounts as $account)
                            @if($account->subAccounts->count() > 0)
                                {{-- Main account with sub-accounts - disabled and shown as header --}}
                                <option value="" disabled style="font-weight: bold; background-color: #f3f4f6; color: #374151;">
                                    {{ $account->code }} - {{ $account->description }} ({{ $account->subAccounts->count() }} sub-accounts - select below)
                                </option>
                                @foreach($account->subAccounts as $subAccount)
                                    <option value="sub_account_{{ $subAccount->id }}" data-type="sub_account" data-id="{{ $subAccount->id }}" style="padding-left: 20px; color: #6366f1;">
                                        ├─ {{ $subAccount->code }} - {{ $subAccount->description }}
                                    </option>
                                @endforeach
                            @else
                                {{-- Main account without sub-accounts - selectable --}}
                                <option value="account_{{ $account->id }}" data-type="account" data-id="{{ $account->id }}">
                                    {{ $account->code }} - {{ $account->description }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select accounts or sub-accounts. Main accounts with sub-accounts cannot be selected - you must choose a specific sub-account.</p>
                    <x-input-error :messages="$errors->get('account_or_sub_account')" class="mt-1" />
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label value="Amount (₱)" />
                    <x-input type="text" name="amount_display" id="amountDisplayInput" placeholder="0.00" class="mt-1 block w-full" />
                    <input type="hidden" name="amount" id="amountInput" />
                    <p class="text-xs text-gray-500 mt-1">Enter amount with commas (e.g., 100,000,900.00)</p>
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>
            </div>

            <!-- Hidden fields for form submission -->
            <input type="hidden" id="accountTypeHidden" name="account_type" value="">
            <input type="hidden" id="accountIdHidden" name="account_id" value="">
            <input type="hidden" id="subAccountIdHidden" name="sub_account_id" value="">

            <!-- Description -->
            <div class="mb-6">
                <x-input-label value="Description (Optional)" />
                <x-input type="text" name="description" id="descriptionInput" placeholder="Enter description..." class="mt-1 block w-full" />
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
            const modal = document.getElementById('allocationModal');
            modal.classList.remove('hidden');
            
            // If there are validation errors, restore the formatted amount
            @if(old('amount'))
                const oldAmount = '{{ old('amount') }}';
                amountInput.value = oldAmount;
                amountDisplayInput.value = formatNumber(oldAmount);
            @endif
        @endif

        const addBtn = document.getElementById('addAllocationBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const closeModal = document.getElementById('closeModal');
        const modal = document.getElementById('allocationModal');

        const formElement = document.getElementById('formElement');
        const formTitle = document.getElementById('formTitle');
        const submitBtn = document.getElementById('submitBtn');

        const accountSubAccountInput = document.getElementById('accountSubAccountInput');
        const accountTypeHidden = document.getElementById('accountTypeHidden');
        const accountIdHidden = document.getElementById('accountIdHidden');
        const subAccountIdHidden = document.getElementById('subAccountIdHidden');
        const amountInput = document.getElementById('amountInput');
        const amountDisplayInput = document.getElementById('amountDisplayInput');
        const descriptionInput = document.getElementById('descriptionInput');

    // Handle account/sub-account selection
    function handleAccountSubAccountSelection() {
        const selectedValue = accountSubAccountInput.value;
        
        if (selectedValue) {
            const selectedOption = accountSubAccountInput.options[accountSubAccountInput.selectedIndex];
            const type = selectedOption.getAttribute('data-type');
            const id = selectedOption.getAttribute('data-id');
            
            // Check if this is a disabled option (shouldn't happen, but extra safety)
            if (selectedOption.disabled) {
                alert('This main account has sub-accounts and cannot be selected. Please choose one of its sub-accounts.');
                accountSubAccountInput.value = '';
                return;
            }
            
            if (type === 'account') {
                // Selected a main account (only allowed if it has no sub-accounts)
                accountTypeHidden.value = 'account';
                accountIdHidden.value = id;
                subAccountIdHidden.value = '';
            } else if (type === 'sub_account') {
                // Selected a sub-account
                accountTypeHidden.value = 'sub_account';
                accountIdHidden.value = '';
                subAccountIdHidden.value = id;
            }
        } else {
            // Clear all hidden fields
            accountTypeHidden.value = '';
            accountIdHidden.value = '';
            subAccountIdHidden.value = '';
        }
    }

    // Event listener for the single dropdown
    accountSubAccountInput.addEventListener('change', handleAccountSubAccountSelection);

    // Amount formatting functions
    function formatNumber(num, forceDecimals = false) {
        if (!num) return '';
        
        const numStr = num.toString().replace(/,/g, '');
        const cleanNum = parseFloat(numStr);
        if (isNaN(cleanNum)) return '';
        
        // Check if original input has decimal point or if we should force decimals
        const hasDecimalPoint = numStr.includes('.');
        const shouldShowDecimals = forceDecimals || hasDecimalPoint;
        
        // Format with commas
        return cleanNum.toLocaleString('en-US', {
            minimumFractionDigits: shouldShowDecimals ? 2 : 0,
            maximumFractionDigits: 2
        });
    }

    function parseNumber(formattedNum) {
        if (!formattedNum) return '';
        // Remove commas and return clean number
        return formattedNum.replace(/,/g, '');
    }

    // Amount input formatting
    amountDisplayInput.addEventListener('input', function() {
        const cursorPosition = this.selectionStart;
        const oldValue = this.value;
        const cleanValue = this.value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        const parts = cleanValue.split('.');
        if (parts.length > 2) {
            parts.splice(2);
        }
        if (parts[1] && parts[1].length > 2) {
            parts[1] = parts[1].substring(0, 2);
        }
        const finalValue = parts.join('.');
        
        // Only format if there's a value
        if (finalValue) {
            const formattedValue = formatNumber(finalValue);
            this.value = formattedValue;
            
            // Update hidden field with clean number (remove commas)
            amountInput.value = finalValue; // Use the clean value directly instead of parsing formatted value
            
            // Restore cursor position (approximately)
            const newCursorPosition = cursorPosition + (formattedValue.length - oldValue.length);
            this.setSelectionRange(newCursorPosition, newCursorPosition);
        } else {
            // Clear both fields if empty
            this.value = '';
            amountInput.value = '';
        }
    });

    // Handle paste events
    amountDisplayInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            const cleanValue = this.value.replace(/[^0-9.]/g, '');
            if (cleanValue) {
                const formattedValue = formatNumber(cleanValue);
                this.value = formattedValue;
                amountInput.value = cleanValue; // Use clean value directly
            } else {
                this.value = '';
                amountInput.value = '';
            }
        }, 0);
    });

    // Form submission handler to ensure clean amount value
    formElement.addEventListener('submit', function(e) {
        // Ensure the hidden amount field has clean value (no commas)
        const displayValue = amountDisplayInput.value;
        if (displayValue) {
            const cleanValue = displayValue.replace(/[^0-9.]/g, '');
            amountInput.value = cleanValue;
        }
    });

    // Show modal for Add
    addBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        formTitle.textContent = 'Add Budget Allocation';
        submitBtn.textContent = 'Add';
        formElement.action = "{{ route('department-expense-type-allocations.store', [$department->id, $expenseType->id]) }}";

        // Reset form
        accountSubAccountInput.value = '';
        accountTypeHidden.value = '';
        accountIdHidden.value = '';
        subAccountIdHidden.value = '';
        amountInput.value = '';
        amountDisplayInput.value = '';
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
            formTitle.textContent = 'Edit Budget Allocation';
            submitBtn.textContent = 'Update';
            formElement.action = `/departments/{{ $department->id }}/expense-types/{{ $expenseType->id }}/allocations/${button.dataset.id}`;

            // Populate form with data attributes
            const year = button.dataset.year;
            const accountType = button.dataset.accountType;
            const accountId = button.dataset.accountId;
            const subAccountId = button.dataset.subAccountId;
            
            // Set year
            document.getElementById('yearInput').value = year;
            
            // Set the dropdown value based on account type
            if (accountType === 'account' && accountId) {
                accountSubAccountInput.value = `account_${accountId}`;
            } else if (accountType === 'sub_account' && subAccountId) {
                accountSubAccountInput.value = `sub_account_${subAccountId}`;
            }
            
            // Trigger the change event to populate hidden fields
            handleAccountSubAccountSelection();
            
            // Set amount values (both display and hidden)
            const amount = button.dataset.amount;
            amountInput.value = amount; // Clean number for hidden field
            amountDisplayInput.value = formatNumber(amount, true); // Force decimals for existing amounts
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
    });
</script>

</x-app-layout>