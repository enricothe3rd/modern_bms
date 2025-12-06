<x-app-layout>
    @section('title', 'Obligation Requests')

    <x-dashboard-header
        title="Obligation Requests (OBR)"
        subtitle="Create and manage obligation requests"
    />

    <div class="max-w-7xl mx-auto p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-900">Obligation Requests</h2>
            <button id="addObrBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                + Create OBR
            </button>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- OBR Table -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6">
            <table id="obrTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">OBR Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsibility Center</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Claimant Payee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obligation Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($obligationRequests as $obr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $obr->obr_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $obr->department->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $obr->claimantPayee->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $obr->obligation_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">₱{{ number_format($obr->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($obr->status === 'approved') bg-green-100 text-green-800
                                    @elseif($obr->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($obr->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('obligation-requests.show', $obr->id) }}" 
                                       class="text-blue-600 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50"
                                       title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50"
                                        data-id="{{ $obr->id }}"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('obligation-requests.destroy', $obr->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50"
                                            onclick="return confirm('Are you sure you want to delete this OBR?')"
                                            title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No obligation requests found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal -->
    <div id="obrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl p-6 relative max-h-[90vh] overflow-y-auto">

                <!-- Close Button -->
                <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

                <!-- Modal Title -->
                <h2 class="text-2xl font-bold mb-4" id="formTitle">Create Obligation Request</h2>

                <!-- Form -->
                <form id="formElement" method="POST" action="{{ route('obligation-requests.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- OBR Number -->
                        <div>
                            <x-input-label value="OBR Number *" />
                            <x-input type="text" name="obr_number" id="obrNumberInput" placeholder="OBR-2025-001" class="mt-1 block w-full" required />
                        </div>

                        <!-- Responsibility Center (Department) -->
                        <div>
                            <x-input-label value="Responsibility Center *" />
                            <select name="department_id" id="departmentInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2" required>
                                <option value="">Select Responsibility Center</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Claimant Payee -->
                        <div>
                            <x-input-label value="Claimant Payee *" />
                            <select name="claimant_payee_id" id="claimantPayeeInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2" required>
                                <option value="">Select Claimant Payee</option>
                                @foreach($claimantPayees as $payee)
                                    <option value="{{ $payee->id }}">{{ $payee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Obligation Date -->
                        <div>
                            <x-input-label value="Obligation Date *" />
                            <x-input type="date" name="obligation_date" id="obligationDateInput" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <!-- Particulars -->
                    <div class="mb-6">
                        <x-input-label value="Particulars *" />
                        <textarea name="particulars" id="particularsInput" rows="3" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2" required></textarea>
                    </div>

                    <!-- Signatories Section -->
                    <div class="border-t pt-4 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Signatories</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Signatory 1 -->
                            <div>
                                <x-input-label value="Signatory 1" />
                                <select name="signatory_1_id" id="signatory1Input" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2">
                                    <option value="">Select Signatory</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Signatory 1 Date" />
                                <x-input type="date" name="signatory_1_date" id="signatory1DateInput" class="mt-1 block w-full" />
                            </div>

                            <!-- Signatory 2 -->
                            <div>
                                <x-input-label value="Signatory 2" />
                                <select name="signatory_2_id" id="signatory2Input" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2">
                                    <option value="">Select Signatory</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Signatory 2 Date" />
                                <x-input type="date" name="signatory_2_date" id="signatory2DateInput" class="mt-1 block w-full" />
                            </div>

                            <!-- Noted Signatory -->
                            <div>
                                <x-input-label value="Noted By" />
                                <select name="noted_signatory_id" id="notedSignatoryInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2">
                                    <option value="">Select Noted By</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Noted Date" />
                                <x-input type="date" name="noted_signatory_date" id="notedSignatoryDateInput" class="mt-1 block w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Optional Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label value="Optional Field 1" />
                            <x-input type="text" name="optional_field_1" id="optionalField1Input" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Optional Field 2" />
                            <x-input type="text" name="optional_field_2" id="optionalField2Input" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <!-- Fund Type and Year Selection (Once for all items) -->
                    <div class="border-t pt-4 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Budget Information (Optional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                            <div>
                                <x-input-label value="Fund Type" />
                                <select name="fund_type_id" id="fundTypeInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2">
                                    <option value="">Select Fund Type</option>
                                    @foreach($fundTypes as $ft)
                                        <option value="{{ $ft->id }}">{{ $ft->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Budget Year" />
                                <select name="budget_year" id="budgetYearInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2">
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
                    </div>

                    <!-- Items Section -->
                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Line Items</h3>
                            <button type="button" id="addItemBtn" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                + Add Item
                            </button>
                        </div>
                        <div id="itemsContainer" class="space-y-4">
                            <!-- Items will be added here dynamically -->
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelBtn" class="px-6 py-3 rounded-lg border border-gray-300">Cancel</button>
                        <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">Create</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    let itemIndex = 0;

    $(document).ready(function() {
        @if($obligationRequests->count() > 0)
        $('#obrTable').DataTable({
            dom: "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'<'buttons'B><'search-wrapper'f>>" +
                 "rt" +
                 "<'flex flex-col sm:flex-row justify-between items-center mt-4 gap-4'<'info'i><'pagination'p>>",
            buttons: {
                dom: {
                    button: {
                        className: 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50'
                    }
                },
                buttons: ['excel', 'csv', 'pdf', 'print']
            },
            pageLength: 10,
            responsive: true,
        });

        // Style DataTables elements
        $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-64');
        $('.dt-buttons').addClass('flex flex-wrap gap-2');
        @endif
    });

    const addBtn = document.getElementById('addObrBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('obrModal');
    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const addItemBtn = document.getElementById('addItemBtn');
    const itemsContainer = document.getElementById('itemsContainer');

    // Show modal for Add
    addBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        formTitle.textContent = 'Create Obligation Request';
        submitBtn.textContent = 'Create';
        formElement.action = "{{ route('obligation-requests.store') }}";
        formElement.reset();
        itemsContainer.innerHTML = '';
        itemIndex = 0;
        addItem(); // Add first item by default

        const putMethod = formElement.querySelector('input[name="_method"]');
        if (putMethod) putMethod.remove();
    });

    // Edit buttons
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', async function() {
            const obrId = this.dataset.id;
            
            try {
                // Fetch OBR data with JSON headers
                const response = await fetch(`/obligation-requests/${obrId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Show modal
                modal.classList.remove('hidden');
                formTitle.textContent = 'Edit Obligation Request';
                submitBtn.textContent = 'Update';
                formElement.action = `/obligation-requests/${obrId}`;
                
                // Add PUT method
                let putMethod = formElement.querySelector('input[name="_method"]');
                if (!putMethod) {
                    putMethod = document.createElement('input');
                    putMethod.type = 'hidden';
                    putMethod.name = '_method';
                    putMethod.value = 'PUT';
                    formElement.appendChild(putMethod);
                }
                
                // Populate form fields
                document.getElementById('obrNumberInput').value = data.obr.obr_number;
                document.getElementById('departmentInput').value = data.obr.department_id;
                document.getElementById('claimantPayeeInput').value = data.obr.claimant_payee_id;
                document.getElementById('obligationDateInput').value = data.obr.obligation_date;
                document.getElementById('particularsInput').value = data.obr.particulars;
                document.getElementById('optionalField1Input').value = data.obr.optional_field_1 || '';
                document.getElementById('optionalField2Input').value = data.obr.optional_field_2 || '';
                
                // Populate signatories
                const sig1 = data.signatories.find(s => s.signatory_type === 'signatory_1');
                const sig2 = data.signatories.find(s => s.signatory_type === 'signatory_2');
                const noted = data.signatories.find(s => s.signatory_type === 'noted');
                
                document.getElementById('signatory1Input').value = sig1 ? sig1.user_id : '';
                document.getElementById('signatory1DateInput').value = sig1 ? sig1.signatory_date : '';
                document.getElementById('signatory2Input').value = sig2 ? sig2.user_id : '';
                document.getElementById('signatory2DateInput').value = sig2 ? sig2.signatory_date : '';
                document.getElementById('notedSignatoryInput').value = noted ? noted.user_id : '';
                document.getElementById('notedSignatoryDateInput').value = noted ? noted.signatory_date : '';
                
                // Populate fund type and budget year
                const firstItem = data.items[0];
                if (firstItem && firstItem.fund_type_id) {
                    document.getElementById('fundTypeInput').value = firstItem.fund_type_id;
                }
                document.getElementById('budgetYearInput').value = data.budget_year || new Date().getFullYear();
                
                // Clear and populate items
                itemsContainer.innerHTML = '';
                itemIndex = 0;
                
                const fundTypeId = document.getElementById('fundTypeInput').value;
                
                for (const item of data.items) {
                    addItem();
                    const row = itemsContainer.lastElementChild;
                    
                    // Load departments first if fund type is set
                    if (fundTypeId) {
                        const deptRes = await fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments`);
                        const departments = await deptRes.json();
                        const deptSelect = row.querySelector('.department-select');
                        deptSelect.innerHTML = '<option value="">Select Department</option>';
                        departments.forEach(dept => {
                            deptSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                        });
                        deptSelect.value = item.department_id;
                    } else {
                        // If no fund type, just set the department value
                        row.querySelector('.department-select').value = item.department_id;
                    }
                    
                    // Load and set expense type
                    if (fundTypeId && item.department_id) {
                        const expRes = await fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${item.department_id}/expense-types`);
                        const expTypes = await expRes.json();
                        const expSelect = row.querySelector('.expense-type-select');
                        expSelect.innerHTML = '<option value="">Select Expense Type</option>';
                        expTypes.forEach(exp => {
                            expSelect.innerHTML += `<option value="${exp.id}">${exp.description}</option>`;
                        });
                        expSelect.value = item.expense_type_id;
                        
                        // Load and set account
                        const budgetYear = document.getElementById('budgetYearInput').value;
                        const accRes = await fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${item.department_id}/expense-types/${item.expense_type_id}/accounts?year=${budgetYear}`);
                        const accounts = await accRes.json();
                        const accSelect = row.querySelector('.account-select');
                        accSelect.innerHTML = '<option value="">Select Account or Sub Account</option>';
                        
                        accounts.forEach(acc => {
                            const mainOption = document.createElement('option');
                            mainOption.value = `account_${acc.id}`;
                            const amountText = acc.is_selectable ? ` [₱${parseFloat(acc.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]` : '';
                            mainOption.textContent = `${acc.code} - ${acc.description}${amountText}`;
                            mainOption.disabled = !acc.is_selectable;
                            mainOption.style.fontWeight = 'bold';
                            accSelect.appendChild(mainOption);
                            
                            if (acc.sub_accounts && acc.sub_accounts.length > 0) {
                                acc.sub_accounts.forEach(sub => {
                                    const subOption = document.createElement('option');
                                    subOption.value = `sub_account_${sub.id}`;
                                    const subAmountText = ` [₱${parseFloat(sub.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]`;
                                    subOption.textContent = `  ↳ ${sub.code} - ${sub.description}${subAmountText}`;
                                    accSelect.appendChild(subOption);
                                });
                            }
                        });
                        
                        // Set selected account or sub-account
                        if (item.sub_account_id) {
                            accSelect.value = `sub_account_${item.sub_account_id}`;
                            row.querySelector('.sub-account-id-input').value = item.sub_account_id;
                        } else if (item.account_id) {
                            accSelect.value = `account_${item.account_id}`;
                            row.querySelector('.account-id-input').value = item.account_id;
                        }
                    }
                    
                    // Set amount
                    row.querySelector('input[type="number"]').value = item.amount;
                }
                
            } catch (error) {
                console.error('Error loading OBR data:', error);
                alert('Error loading obligation request data');
            }
        });
    });

    // Cancel / Close modal
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));

    // Add Item
    addItemBtn.addEventListener('click', addItem);

    function addItem() {
        const itemHtml = `
            <div class="item-row border rounded-lg p-4 bg-gray-50" data-index="${itemIndex}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold">Item #<span class="item-number">${itemIndex + 1}</span></h4>
                    <button type="button" class="remove-item text-red-600 hover:text-red-700 text-sm">Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                        <select name="items[${itemIndex}][department_id]" class="department-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">Select Department</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expense Type *</label>
                        <select name="items[${itemIndex}][expense_type_id]" class="expense-type-select w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">Select Expense Type</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account / Sub Account *</label>
                        <select name="items[${itemIndex}][account_or_sub_id]" class="account-select w-full border border-gray-300 rounded-lg px-3 py-2" data-account-type="" required>
                            <option value="">Select Account or Sub Account</option>
                        </select>
                        <input type="hidden" name="items[${itemIndex}][account_id]" class="account-id-input">
                        <input type="hidden" name="items[${itemIndex}][sub_account_id]" class="sub-account-id-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="items[${itemIndex}][amount]" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                </div>
            </div>
        `;
        
        itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
        itemIndex++;
        updateItemNumbers();
        attachItemEventListeners();
        
        // Load departments if fund type is already selected
        const fundTypeId = document.getElementById('fundTypeInput').value;
        if (fundTypeId) {
            const newRow = itemsContainer.lastElementChild;
            const deptSelect = newRow.querySelector('.department-select');
            
            fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments`)
                .then(res => res.json())
                .then(data => {
                    deptSelect.innerHTML = '<option value="">Select Department</option>';
                    data.forEach(dept => {
                        deptSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                    });
                })
                .catch(error => console.error('Error loading departments:', error));
        }
    }

    function updateItemNumbers() {
        document.querySelectorAll('.item-row').forEach((row, index) => {
            row.querySelector('.item-number').textContent = index + 1;
        });
    }

    function attachItemEventListeners() {
        // Event listeners are now handled via event delegation
    }

    // Use event delegation for dynamic elements
    itemsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                updateItemNumbers();
            } else {
                alert('At least one item is required');
            }
        }
    });

    // Single Fund Type change -> Load Departments for all items
    const fundTypeInput = document.getElementById('fundTypeInput');
    fundTypeInput.addEventListener('change', function() {
        const fundTypeId = this.value;
        
        // Update all department dropdowns in all items
        document.querySelectorAll('.department-select').forEach(deptSelect => {
            if (fundTypeId) {
                fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments`)
                    .then(res => res.json())
                    .then(data => {
                        deptSelect.innerHTML = '<option value="">Select Department</option>';
                        data.forEach(dept => {
                            deptSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                        });
                    });
            } else {
                deptSelect.innerHTML = '<option value="">Select Department</option>';
            }
            
            // Reset dependent dropdowns for this row
            const row = deptSelect.closest('.item-row');
            row.querySelector('.expense-type-select').innerHTML = '<option value="">Select Expense Type</option>';
            row.querySelector('.account-select').innerHTML = '<option value="">Select Account or Sub Account</option>';
            row.querySelector('.account-id-input').value = '';
            row.querySelector('.sub-account-id-input').value = '';
        });
    });

    // Budget Year change -> Reload accounts for all items that have expense type selected
    const budgetYearInput = document.getElementById('budgetYearInput');
    budgetYearInput.addEventListener('change', function() {
        // Reload accounts for each item that has expense type selected
        document.querySelectorAll('.item-row').forEach(row => {
            const fundTypeId = document.getElementById('fundTypeInput').value;
            const deptId = row.querySelector('.department-select').value;
            const expenseTypeId = row.querySelector('.expense-type-select').value;
            const accountSelect = row.querySelector('.account-select');
            
            // Only reload if expense type is selected
            if (fundTypeId && deptId && expenseTypeId) {
                const budgetYear = this.value;
                fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${deptId}/expense-types/${expenseTypeId}/accounts?year=${budgetYear}`)
                    .then(res => res.json())
                    .then(data => {
                        accountSelect.innerHTML = '<option value="">Select Account or Sub Account</option>';
                        
                        data.forEach(acc => {
                            // Add main account (disabled if has sub-accounts)
                            const mainOption = document.createElement('option');
                            mainOption.value = `account_${acc.id}`;
                            const amountText = acc.is_selectable ? ` [₱${parseFloat(acc.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]` : '';
                            mainOption.textContent = `${acc.code} - ${acc.description}${amountText}`;
                            mainOption.disabled = !acc.is_selectable;
                            mainOption.style.fontWeight = 'bold';
                            accountSelect.appendChild(mainOption);
                            
                            // Add sub-accounts if any
                            if (acc.sub_accounts && acc.sub_accounts.length > 0) {
                                acc.sub_accounts.forEach(sub => {
                                    const subOption = document.createElement('option');
                                    subOption.value = `sub_account_${sub.id}`;
                                    const subAmountText = ` [₱${parseFloat(sub.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]`;
                                    subOption.textContent = `  ↳ ${sub.code} - ${sub.description}${subAmountText}`;
                                    accountSelect.appendChild(subOption);
                                });
                            }
                        });
                    });
                
                // Reset hidden fields
                row.querySelector('.account-id-input').value = '';
                row.querySelector('.sub-account-id-input').value = '';
            }
        });
    });

    // Item-level event delegation
    itemsContainer.addEventListener('change', function(e) {
        // Department change -> Load Expense Types
        if (e.target.classList.contains('department-select')) {
            const row = e.target.closest('.item-row');
            const fundTypeId = document.getElementById('fundTypeInput').value;
            const deptId = e.target.value;
            const expenseSelect = row.querySelector('.expense-type-select');
            
            if (fundTypeId && deptId) {
                fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${deptId}/expense-types`)
                    .then(res => res.json())
                    .then(data => {
                        expenseSelect.innerHTML = '<option value="">Select Expense Type</option>';
                        data.forEach(exp => {
                            expenseSelect.innerHTML += `<option value="${exp.id}">${exp.description}</option>`;
                        });
                    });
            } else {
                expenseSelect.innerHTML = '<option value="">Select Expense Type</option>';
            }
            
            // Reset dependent dropdowns
            row.querySelector('.account-select').innerHTML = '<option value="">Select Account or Sub Account</option>';
            row.querySelector('.account-id-input').value = '';
            row.querySelector('.sub-account-id-input').value = '';
        }

        // Expense Type change -> Load Accounts and Sub Accounts
        if (e.target.classList.contains('expense-type-select')) {
            const row = e.target.closest('.item-row');
            const fundTypeId = document.getElementById('fundTypeInput').value;
            const deptId = row.querySelector('.department-select').value;
            const expenseTypeId = e.target.value;
            const accountSelect = row.querySelector('.account-select');
            
            if (fundTypeId && deptId && expenseTypeId) {
                const budgetYear = document.getElementById('budgetYearInput').value;
                fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${deptId}/expense-types/${expenseTypeId}/accounts?year=${budgetYear}`)
                    .then(res => res.json())
                    .then(data => {
                        accountSelect.innerHTML = '<option value="">Select Account or Sub Account</option>';
                        
                        data.forEach(acc => {
                            // Add main account (disabled if has sub-accounts)
                            const mainOption = document.createElement('option');
                            mainOption.value = `account_${acc.id}`;
                            const amountText = acc.is_selectable ? ` [₱${parseFloat(acc.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]` : '';
                            mainOption.textContent = `${acc.code} - ${acc.description}${amountText}`;
                            mainOption.disabled = !acc.is_selectable;
                            mainOption.style.fontWeight = 'bold';
                            accountSelect.appendChild(mainOption);
                            
                            // Add sub-accounts if any
                            if (acc.sub_accounts && acc.sub_accounts.length > 0) {
                                acc.sub_accounts.forEach(sub => {
                                    const subOption = document.createElement('option');
                                    subOption.value = `sub_account_${sub.id}`;
                                    const subAmountText = ` [₱${parseFloat(sub.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]`;
                                    subOption.textContent = `  ↳ ${sub.code} - ${sub.description}${subAmountText}`;
                                    accountSelect.appendChild(subOption);
                                });
                            }
                        });
                    });
            } else {
                accountSelect.innerHTML = '<option value="">Select Account or Sub Account</option>';
            }
        }

        // Account/Sub Account selection -> Update hidden fields
        if (e.target.classList.contains('account-select')) {
            const row = e.target.closest('.item-row');
            const selectedValue = e.target.value;
            const accountIdInput = row.querySelector('.account-id-input');
            const subAccountIdInput = row.querySelector('.sub-account-id-input');
            
            // Reset hidden fields
            accountIdInput.value = '';
            subAccountIdInput.value = '';
            
            if (selectedValue) {
                const [type, id] = selectedValue.split('_');
                if (type === 'account') {
                    accountIdInput.value = id;
                } else if (type === 'sub') {
                    // Extract the actual ID (format is "sub_account_123")
                    const actualId = selectedValue.replace('sub_account_', '');
                    subAccountIdInput.value = actualId;
                }
            }
        }
    });
    </script>
    @endpush

</x-app-layout>
