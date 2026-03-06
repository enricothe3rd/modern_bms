<x-app-layout>
<x-dashboard-header
    title="Create Supplemental Budget"
    subtitle="Create a new supplemental budget allocation."
/>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <!-- Header space -->
        </div>
        <x-secondary-button onclick="window.location.href='{{ route('supplemental-budgets.index') }}'">
            Back to List
        </x-secondary-button>
    </div>

    <form action="{{ route('supplemental-budgets.store') }}" method="POST" id="supplementalBudgetForm">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="title" value="Title" required />
                    <x-input 
                        type="text" 
                        name="title" 
                        id="title" 
                        value="{{ old('title') }}" 
                        class="mt-1 @error('title') border-red-500 @enderror"
                        required 
                    />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="fiscal_year_id" value="Fiscal Year" required />
                    <select name="fiscal_year_id" id="fiscal_year_id" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mt-1 @error('fiscal_year_id') border-red-500 @enderror"
                            required>
                        <option value="">Select Fiscal Year</option>
                        @foreach($fiscalYears as $fiscalYear)
                            <option value="{{ $fiscalYear->id }}" {{ old('fiscal_year_id', $currentFiscalYear?->id) == $fiscalYear->id ? 'selected' : '' }}>
                                {{ $fiscalYear->year }}
                                @if($fiscalYear->is_current) (Current) @endif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('fiscal_year_id')" class="mt-2" />
                </div>
                </div>

                <div>
                    <x-input-label for="supplemental_group" value="Supplemental Group" required />
                    <x-input 
                        type="text" 
                        name="supplemental_group" 
                        id="supplemental_group" 
                        value="{{ old('supplemental_group') }}" 
                        placeholder="e.g., Supplemental Budget 1, Mid-Year Adjustment"
                        class="mt-1 @error('supplemental_group') border-red-500 @enderror"
                        required 
                    />
                    <x-input-error :messages="$errors->get('supplemental_group')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6">
                <x-input-label for="description" value="Description" />
                <textarea name="description" id="description" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mt-1 @error('description') border-red-500 @enderror"
                          placeholder="Optional description of the supplemental budget">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <!-- Budget Items -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Budget Items</h2>
                <div class="flex gap-2">
                    <div>
                        <x-input-label value="Fund Type" />
                        <select id="fundTypeInput" class="mt-1 block w-48 border border-neutral-300 rounded-lg px-4 py-2">
                            <option value="">Select Fund Type</option>
                            @foreach($fundTypes as $fundType)
                                <option value="{{ $fundType->id }}">{{ $fundType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" id="addItem" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Add Item
                    </button>
                </div>
            </div>

            <div id="itemsContainer">
                <!-- Items will be added here dynamically -->
            </div>

            <x-input-error :messages="$errors->get('items')" class="mt-2" />
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <x-secondary-button onclick="window.location.href='{{ route('supplemental-budgets.index') }}'">
                Cancel
            </x-secondary-button>
            <x-primary-button type="submit">
                Create Supplemental Budget
            </x-primary-button>
        </div>
    </form>
</div>

<!-- Item Template -->
<template id="itemTemplate">
    <div class="item-row border border-gray-200 rounded-lg p-4 mb-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Budget Item</h3>
            <button type="button" class="remove-item text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                <select name="items[INDEX][department_id]" class="department-select w-full border border-gray-300 rounded-md px-3 py-2" required>
                    <option value="">Select Department</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expense Type *</label>
                <select name="items[INDEX][expense_type_id]" class="expense-type-select w-full border border-gray-300 rounded-md px-3 py-2" required>
                    <option value="">Select Expense Type</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account *</label>
                <select name="items[INDEX][account_id]" class="account-select w-full border border-gray-300 rounded-md px-3 py-2" required>
                    <option value="">Select Account</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sub Account</label>
                <select name="items[INDEX][sub_account_id]" class="sub-account-select w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Select Sub Account</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                <input type="number" name="items[INDEX][amount]" step="0.01" min="0.01" 
                       class="amount-input w-full border border-gray-300 rounded-md px-3 py-2" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Justification</label>
                <textarea name="items[INDEX][justification]" rows="2" 
                          class="w-full border border-gray-300 rounded-md px-3 py-2"
                          placeholder="Justification for this budget item"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                <textarea name="items[INDEX][remarks]" rows="2" 
                          class="w-full border border-gray-300 rounded-md px-3 py-2"
                          placeholder="Additional remarks"></textarea>
            </div>
        </div>
    </div>
</template>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItem');
    const itemTemplate = document.getElementById('itemTemplate');
    const fundTypeInput = document.getElementById('fundTypeInput');

    // Add first item by default
    addItem();

    addItemBtn.addEventListener('click', addItem);

    // Fund type change handler
    fundTypeInput.addEventListener('change', function() {
        const fundTypeId = this.value;
        
        // Update all existing items
        document.querySelectorAll('.item-row').forEach(itemRow => {
            const deptSelect = itemRow.querySelector('.department-select');
            const expenseTypeSelect = itemRow.querySelector('.expense-type-select');
            const accountSelect = itemRow.querySelector('.account-select');
            const subAccountSelect = itemRow.querySelector('.sub-account-select');
            
            // Clear all dependent selects
            expenseTypeSelect.innerHTML = '<option value="">Select Expense Type</option>';
            accountSelect.innerHTML = '<option value="">Select Account</option>';
            subAccountSelect.innerHTML = '<option value="">Select Sub Account</option>';
            
            // Load departments for this fund type
            if (fundTypeId) {
                loadDepartments(deptSelect, fundTypeId);
            } else {
                deptSelect.innerHTML = '<option value="">Select Department</option>';
            }
        });
    });

    function addItem() {
        const template = itemTemplate.content.cloneNode(true);
        const itemDiv = template.querySelector('.item-row');
        
        // Replace INDEX with actual index
        itemDiv.innerHTML = itemDiv.innerHTML.replace(/INDEX/g, itemIndex);
        
        // Add remove functionality
        const removeBtn = itemDiv.querySelector('.remove-item');
        removeBtn.addEventListener('click', function() {
            if (itemsContainer.children.length > 1) {
                itemDiv.remove();
                updateTotal();
            } else {
                alert('At least one budget item is required.');
            }
        });

        // Get selects
        const deptSelect = itemDiv.querySelector('.department-select');
        const expenseTypeSelect = itemDiv.querySelector('.expense-type-select');
        const accountSelect = itemDiv.querySelector('.account-select');
        const subAccountSelect = itemDiv.querySelector('.sub-account-select');
        const amountInput = itemDiv.querySelector('.amount-input');

        // Load departments if fund type is selected
        const fundTypeId = fundTypeInput.value;
        if (fundTypeId) {
            loadDepartments(deptSelect, fundTypeId);
        }

        // Department change handler
        deptSelect.addEventListener('change', function() {
            const departmentId = this.value;
            const fundTypeId = fundTypeInput.value;
            
            // Clear dependent selects
            expenseTypeSelect.innerHTML = '<option value="">Select Expense Type</option>';
            accountSelect.innerHTML = '<option value="">Select Account</option>';
            subAccountSelect.innerHTML = '<option value="">Select Sub Account</option>';
            
            if (fundTypeId && departmentId) {
                loadExpenseTypes(expenseTypeSelect, fundTypeId, departmentId);
            }
        });

        // Expense type change handler
        expenseTypeSelect.addEventListener('change', function() {
            const expenseTypeId = this.value;
            const departmentId = deptSelect.value;
            const fundTypeId = fundTypeInput.value;
            const fiscalYearId = document.getElementById('fiscal_year_id').value;
            
            // Clear dependent selects
            accountSelect.innerHTML = '<option value="">Select Account</option>';
            subAccountSelect.innerHTML = '<option value="">Select Sub Account</option>';
            
            if (fundTypeId && departmentId && expenseTypeId && fiscalYearId) {
                loadAccounts(accountSelect, fundTypeId, departmentId, expenseTypeId, fiscalYearId);
            }
        });

        // Account change handler for sub-accounts
        accountSelect.addEventListener('change', function() {
            const accountId = this.value;
            
            // Clear sub-account select
            subAccountSelect.innerHTML = '<option value="">Select Sub Account</option>';
            
            if (accountId) {
                fetch(`{{ route('api.sub-accounts') }}?account_id=${accountId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subAccount => {
                            const option = document.createElement('option');
                            option.value = subAccount.id;
                            option.textContent = subAccount.description;
                            subAccountSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching sub-accounts:', error));
            }
        });

        // Add amount change listener
        amountInput.addEventListener('input', updateTotal);

        itemsContainer.appendChild(itemDiv);
        itemIndex++;
    }

    function loadDepartments(selectElement, fundTypeId) {
        fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments`)
            .then(response => response.json())
            .then(data => {
                selectElement.innerHTML = '<option value="">Select Department</option>';
                data.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.name;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching departments:', error));
    }

    function loadExpenseTypes(selectElement, fundTypeId, departmentId) {
        const fiscalYearId = document.getElementById('fiscal_year_id').value;
        fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${departmentId}/expense-types?fiscal_year_id=${fiscalYearId}`)
            .then(response => response.json())
            .then(data => {
                selectElement.innerHTML = '<option value="">Select Expense Type</option>';
                data.forEach(expenseType => {
                    const option = document.createElement('option');
                    option.value = expenseType.id;
                    option.textContent = expenseType.description;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching expense types:', error));
    }

    function loadAccounts(selectElement, fundTypeId, departmentId, expenseTypeId, fiscalYearId) {
        fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${departmentId}/expense-types/${expenseTypeId}/accounts?fiscal_year_id=${fiscalYearId}`)
            .then(response => response.json())
            .then(data => {
                selectElement.innerHTML = '<option value="">Select Account</option>';
                data.forEach(account => {
                    const option = document.createElement('option');
                    option.value = account.id;
                    option.textContent = account.description;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching accounts:', error));
    }

    function updateTotal() {
        const amountInputs = document.querySelectorAll('.amount-input');
        let total = 0;
        
        amountInputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });

        console.log('Total:', total);
    }
});
</script>
<style>
.select2-container .select2-selection--single {
    height: 39px !important;
    border-radius: 0.375rem;
    border-color: #d1d5db;
}

.select2-selection__rendered {
    line-height: 34px !important;
    padding-left: 0.75rem;
}

.select2-selection__arrow {
    height: 36px !important;
    margin-right: 20px;
}

.select2-container--default {
    width: 100%;
}

span .selection {
    width: 100%;
}
</style>
</x-app-layout>