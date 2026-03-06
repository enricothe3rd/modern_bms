<x-app-layout>
    @section('title', 'Create Budget Realignment')

    <x-dashboard-header
        title="Create Budget Realignment"
        subtitle="Transfer budget allocations between departments and expense types"
    />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900">Create Budget Realignment</h2>
                <p class="text-sm text-gray-600 mt-1">Move budget allocations from one or more sources to one or more destinations</p>
            </div>
            <a href="{{ route('budget-realignments.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                ← Back to List
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('budget-realignments.store') }}" method="POST" id="realignmentForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
                            <div class="font-bold mb-2">Please correct the following errors:</div>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label value="Description *" />
                            <textarea name="description" rows="3" 
                                      class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2" 
                                      required>{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <x-input-label value="Fiscal Year *" />
                            <select name="fiscal_year_id" id="fiscalYear" 
                                    class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2" required>
                                <option value="">Select Fiscal Year</option>
                                @foreach($fiscalYears as $fiscalYear)
                                    <option value="{{ $fiscalYear->id }}" 
                                            {{ old('fiscal_year_id', $currentFiscalYear?->id) == $fiscalYear->id ? 'selected' : '' }}>
                                        {{ $fiscalYear->year }}
                                        @if($fiscalYear->is_current) (Current) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Available Options</h3>
                    <p class="text-sm text-gray-600 mt-1">Select from available departments, expense types, and accounts</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label value="Departments Available" />
                            <p class="text-sm text-gray-600">{{ $departments->count() }} departments</p>
                        </div>
                        <div>
                            <x-input-label value="Expense Types Available" />
                            <p class="text-sm text-gray-600">{{ $expenseTypes->count() }} expense types</p>
                        </div>
                        <div>
                            <x-input-label value="Accounts Available" />
                            <p class="text-sm text-gray-600">{{ $accounts->count() }} accounts</p>
                        </div>
                        <div>
                            <x-input-label value="Sub-Accounts Available" />
                            <p class="text-sm text-gray-600">{{ $subAccounts->count() }} sub-accounts</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- From Items (Sources) -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-red-800">From (Source Allocations)</h3>
                            <p class="text-sm text-red-600 mt-1">Select allocations to transfer budget FROM</p>
                        </div>
                        <button type="button" id="addFromItem" 
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                            + Add Source
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="fromItemsContainer" class="space-y-4">
                        <!-- From items will be added here -->
                    </div>
                    <div class="mt-4 p-4 bg-red-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-red-800">Total From Amount:</span>
                            <span id="totalFromAmount" class="font-bold text-red-800">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- To Items (Destinations) -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-green-800">To (Destination Allocations)</h3>
                            <p class="text-sm text-green-600 mt-1">Select allocations to transfer budget TO</p>
                        </div>
                        <button type="button" id="addToItem" 
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                            + Add Destination
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="toItemsContainer" class="space-y-4">
                        <!-- To items will be added here -->
                    </div>
                    <div class="mt-4 p-4 bg-green-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-green-800">Total To Amount:</span>
                            <span id="totalToAmount" class="font-bold text-green-800">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Summary -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div class="p-4 bg-red-50 rounded-lg">
                            <div class="text-sm text-red-600">Total From</div>
                            <div id="summaryFromAmount" class="text-2xl font-bold text-red-800">₱0.00</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-sm text-green-600">Total To</div>
                            <div id="summaryToAmount" class="text-2xl font-bold text-green-800">₱0.00</div>
                        </div>
                        <div class="p-4 rounded-lg" id="balanceCard">
                            <div class="text-sm">Balance</div>
                            <div id="balanceAmount" class="text-2xl font-bold">₱0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('budget-realignments.index') }}" 
                   class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Create Realignment
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    let fromItemIndex = 0;
    let toItemIndex = 0;

    // Pre-generate options HTML for better performance
    const departmentOptions = `
        <option value="">Select Department</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
        @endforeach
    `;
    
    const expenseTypeOptions = `
        <option value="">Select Expense Type</option>
        @foreach($expenseTypes as $expenseType)
            <option value="{{ $expenseType->id }}">{{ $expenseType->description }}</option>
        @endforeach
    `;
    
    const accountOptions = `
        <option value="">Select Account</option>
        @foreach($accounts as $account)
            <option value="{{ $account->id }}">{{ $account->description }}</option>
        @endforeach
    `;
    
    const subAccountOptions = `
        <option value="">Select Sub Account</option>
        @foreach($subAccounts as $subAccount)
            <option value="{{ $subAccount->id }}">{{ $subAccount->description }}</option>
        @endforeach
    `;

    $(document).ready(function() {
        console.log('Document ready - Budget Realignment Create');
        
        // Add initial items
        addFromItem();
        addToItem();
        
        // Add item buttons
        $('#addFromItem').on('click', function() {
            console.log('Add From Item clicked');
            addFromItem();
        });
        $('#addToItem').on('click', function() {
            console.log('Add To Item clicked');
            addToItem();
        });
        
        // Form validation
        $('#realignmentForm').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    });

    function addFromItem() {
        console.log('addFromItem function called, index:', fromItemIndex);
        const html = `
            <div class="from-item border border-red-200 rounded-lg p-4 bg-red-50" data-index="${fromItemIndex}">
                <div class="flex justify-between items-start mb-3">
                    <h4 class="font-semibold text-red-800">Source #${fromItemIndex + 1}</h4>
                    <button type="button" class="remove-from-item text-red-600 hover:text-red-700 text-sm">Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                        <select name="from_items[${fromItemIndex}][department_id]" 
                                class="department-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            ${departmentOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expense Type *</label>
                        <select name="from_items[${fromItemIndex}][expense_type_id]" 
                                class="expense-type-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            ${expenseTypeOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account *</label>
                        <select name="from_items[${fromItemIndex}][account_id]" 
                                class="account-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            ${accountOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sub Account</label>
                        <select name="from_items[${fromItemIndex}][sub_account_id]" 
                                class="sub-account-select w-full border border-gray-300 rounded-lg px-3 py-2">
                            ${subAccountOptions}
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="from_items[${fromItemIndex}][amount]" step="0.01" min="0.01"
                               class="from-amount-input w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea name="from_items[${fromItemIndex}][remarks]" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                    </div>
                </div>
                <div class="allocation-info-display mt-3 p-3 bg-gray-50 rounded-lg hidden">
                    <h5 class="font-medium text-gray-700 mb-2">Allocation Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                        <div>
                            <span class="text-gray-600">Allocated:</span>
                            <span class="allocated-amount font-medium">₱0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Obligated:</span>
                            <span class="obligated-amount font-medium">₱0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Available:</span>
                            <span class="available-amount font-medium text-green-600">₱0.00</span>
                        </div>
                    </div>
                    <div class="allocation-message mt-2 text-xs text-gray-500"></div>
                </div>
            </div>
        `;
        
        $('#fromItemsContainer').append(html);
        fromItemIndex++;
        attachItemEventListeners();
        console.log('From item added, new index:', fromItemIndex);
    }

    function addToItem() {
        console.log('addToItem function called, index:', toItemIndex);
        const html = `
            <div class="to-item border border-green-200 rounded-lg p-4 bg-green-50" data-index="${toItemIndex}">
                <div class="flex justify-between items-start mb-3">
                    <h4 class="font-semibold text-green-800">Destination #${toItemIndex + 1}</h4>
                    <button type="button" class="remove-to-item text-green-600 hover:text-green-700 text-sm">Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                        <select name="to_items[${toItemIndex}][department_id]" 
                                class="department-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            ${departmentOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expense Type *</label>
                        <select name="to_items[${toItemIndex}][expense_type_id]" 
                                class="expense-type-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            ${expenseTypeOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account *</label>
                        <select name="to_items[${toItemIndex}][account_id]" 
                                class="account-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            ${accountOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sub Account</label>
                        <select name="to_items[${toItemIndex}][sub_account_id]" 
                                class="sub-account-select w-full border border-gray-300 rounded-lg px-3 py-2">
                            ${subAccountOptions}
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="to_items[${toItemIndex}][amount]" step="0.01" min="0.01"
                               class="to-amount-input w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea name="to_items[${toItemIndex}][remarks]" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                    </div>
                </div>
                <div class="allocation-info-display mt-3 p-3 bg-gray-50 rounded-lg hidden">
                    <h5 class="font-medium text-gray-700 mb-2">Allocation Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                        <div>
                            <span class="text-gray-600">Allocated:</span>
                            <span class="allocated-amount font-medium">₱0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Obligated:</span>
                            <span class="obligated-amount font-medium">₱0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Available:</span>
                            <span class="available-amount font-medium text-green-600">₱0.00</span>
                        </div>
                    </div>
                    <div class="allocation-message mt-2 text-xs text-gray-500"></div>
                </div>
            </div>
        `;
        
        $('#toItemsContainer').append(html);
        toItemIndex++;
        attachItemEventListeners();
        console.log('To item added, new index:', toItemIndex);
    }

    function attachItemEventListeners() {
        // Remove item buttons
        $('.remove-from-item').off('click').on('click', function() {
            if ($('.from-item').length > 1) {
                $(this).closest('.from-item').remove();
                updateTotals();
            }
        });
        
        $('.remove-to-item').off('click').on('click', function() {
            if ($('.to-item').length > 1) {
                $(this).closest('.to-item').remove();
                updateTotals();
            }
        });
        
        // Amount input changes
        $('.from-amount-input, .to-amount-input').off('input').on('input', updateTotals);
        
        // Account change handler for sub-accounts
        $('.account-select').off('change').on('change', function() {
            const accountId = $(this).val();
            const subAccountSelect = $(this).closest('.from-item, .to-item').find('.sub-account-select');
            
            if (accountId) {
                loadSubAccounts(accountId, subAccountSelect);
            } else {
                subAccountSelect.html('<option value="">Select Sub Account</option>');
            }
            
            // Check allocation info when account changes
            checkAllocationInfo($(this).closest('.from-item, .to-item'));
        });
        
        // Sub-account change handler
        $('.sub-account-select').off('change').on('change', function() {
            checkAllocationInfo($(this).closest('.from-item, .to-item'));
        });
        
        // Department and expense type change handlers
        $('.department-select, .expense-type-select').off('change').on('change', function() {
            checkAllocationInfo($(this).closest('.from-item, .to-item'));
        });
    }

    function loadSubAccounts(accountId, subAccountSelect) {
        $.ajax({
            url: '{{ route("api.sub-accounts") }}',
            method: 'GET',
            data: { account_id: accountId },
            success: function(data) {
                subAccountSelect.html('<option value="">Select Sub Account</option>');
                data.forEach(function(subAccount) {
                    subAccountSelect.append(`<option value="${subAccount.id}">${subAccount.description}</option>`);
                });
            },
            error: function() {
                console.error('Error loading sub-accounts');
            }
        });
    }

    function checkAllocationInfo(itemContainer) {
        const fiscalYearId = $('#fiscalYear').val();
        const departmentId = itemContainer.find('.department-select').val();
        const expenseTypeId = itemContainer.find('.expense-type-select').val();
        const accountId = itemContainer.find('.account-select').val();
        const subAccountId = itemContainer.find('.sub-account-select').val();
        
        const infoDisplay = itemContainer.find('.allocation-info-display');
        
        console.log('checkAllocationInfo called with:', {
            fiscalYearId,
            departmentId,
            expenseTypeId,
            accountId,
            subAccountId
        });
        
        // Hide info if required fields are not selected
        if (!fiscalYearId || !departmentId || !expenseTypeId || !accountId) {
            infoDisplay.addClass('hidden');
            console.log('Required fields missing, hiding info display');
            return;
        }
        
        // Show loading state
        infoDisplay.removeClass('hidden');
        infoDisplay.find('.allocation-message').text('Loading allocation information...');
        
        const requestData = {
            fiscal_year_id: fiscalYearId,
            department_id: departmentId,
            expense_type_id: expenseTypeId,
            account_id: accountId,
            sub_account_id: subAccountId
        };
        
        console.log('Making AJAX request with data:', requestData);
        
        $.ajax({
            url: '{{ route("budget-realignments.allocation-info") }}',
            method: 'GET',
            data: requestData,
            success: function(data) {
                console.log('AJAX success response:', data);
                if (data.exists) {
                    // Update display with allocation info
                    infoDisplay.find('.allocated-amount').text(`₱${parseFloat(data.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                    infoDisplay.find('.obligated-amount').text(`₱${parseFloat(data.obligated_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                    infoDisplay.find('.available-amount').text(`₱${parseFloat(data.available_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                    
                    // Color code the available amount
                    const availableAmountEl = infoDisplay.find('.available-amount');
                    if (data.available_amount > 0) {
                        availableAmountEl.removeClass('text-red-600').addClass('text-green-600');
                    } else {
                        availableAmountEl.removeClass('text-green-600').addClass('text-red-600');
                    }
                    
                    infoDisplay.find('.allocation-message').text(data.message);
                    
                    // Set max amount for from items (source items should not exceed available amount)
                    if (itemContainer.hasClass('from-item')) {
                        const amountInput = itemContainer.find('.from-amount-input');
                        amountInput.attr('max', data.available_amount);
                        
                        if (data.available_amount <= 0) {
                            infoDisplay.find('.allocation-message').text('⚠️ No available amount for this allocation');
                        }
                    }
                } else {
                    // No allocation exists
                    infoDisplay.find('.allocated-amount').text('₱0.00');
                    infoDisplay.find('.obligated-amount').text('₱0.00');
                    infoDisplay.find('.available-amount').text('₱0.00').removeClass('text-green-600').addClass('text-gray-600');
                    infoDisplay.find('.allocation-message').text(data.message + ' - This will create a new allocation');
                    
                    // Remove max limit for from items when no allocation exists
                    if (itemContainer.hasClass('from-item')) {
                        itemContainer.find('.from-amount-input').removeAttr('max');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {xhr, status, error});
                console.error('Response text:', xhr.responseText);
                infoDisplay.find('.allocation-message').text('Error loading allocation information');
            }
        });
    }

    function updateTotals() {
        let totalFrom = 0;
        let totalTo = 0;
        
        $('.from-amount-input').each(function() {
            const value = parseFloat($(this).val()) || 0;
            totalFrom += value;
        });
        
        $('.to-amount-input').each(function() {
            const value = parseFloat($(this).val()) || 0;
            totalTo += value;
        });
        
        const balance = totalFrom - totalTo;
        
        // Update displays
        $('#totalFromAmount, #summaryFromAmount').text(`₱${totalFrom.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
        $('#totalToAmount, #summaryToAmount').text(`₱${totalTo.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
        $('#balanceAmount').text(`₱${Math.abs(balance).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
        
        // Update balance card styling
        const balanceCard = $('#balanceCard');
        const balanceAmountEl = $('#balanceAmount');
        
        if (Math.abs(balance) < 0.01) {
            balanceCard.removeClass('bg-red-50 bg-yellow-50').addClass('bg-green-50');
            balanceAmountEl.removeClass('text-red-800 text-yellow-800').addClass('text-green-800');
        } else {
            balanceCard.removeClass('bg-green-50 bg-yellow-50').addClass('bg-red-50');
            balanceAmountEl.removeClass('text-green-800 text-yellow-800').addClass('text-red-800');
        }
        
        // Enable/disable submit button
        const submitBtn = $('#submitBtn');
        if (Math.abs(balance) < 0.01 && totalFrom > 0 && totalTo > 0) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    }

    function validateForm() {
        const totalFrom = parseFloat($('#totalFromAmount').text().replace(/[₱,]/g, '')) || 0;
        const totalTo = parseFloat($('#totalToAmount').text().replace(/[₱,]/g, '')) || 0;
        
        if (Math.abs(totalFrom - totalTo) > 0.01) {
            alert('Total "From" amount must equal total "To" amount.');
            return false;
        }
        
        if (totalFrom === 0) {
            alert('Please add at least one source allocation with an amount.');
            return false;
        }
        
        // Validate that from items don't exceed available amounts
        let hasExceededAmount = false;
        $('.from-item').each(function() {
            const amountInput = $(this).find('.from-amount-input');
            const maxAmount = parseFloat(amountInput.attr('max'));
            const enteredAmount = parseFloat(amountInput.val()) || 0;
            
            if (maxAmount && enteredAmount > maxAmount) {
                const itemNumber = $(this).find('h4').text();
                alert(`${itemNumber}: Amount (₱${enteredAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}) exceeds available amount (₱${maxAmount.toLocaleString('en-US', {minimumFractionDigits: 2})})`);
                hasExceededAmount = true;
                return false; // Break the loop
            }
        });
        
        if (hasExceededAmount) {
            return false;
        }
        
        return true;
    }
    </script>
    @endpush
</x-app-layout>