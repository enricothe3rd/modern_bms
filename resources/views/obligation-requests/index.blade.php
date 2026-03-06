<x-app-layout>
    @section('title', 'Obligation Requests')

    <x-dashboard-header
        title="Obligation Requests (OBR)"
        subtitle="Create and manage obligation requests"
    />

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        .animate-fade-out {
            animation: fadeOut 0.3s ease-out;
        }
    </style>

    <div class="max-w-7xl mx-auto p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900">Obligation Requests</h2>
                @if(!empty($userAssignedStatusIds))
                    <p class="text-sm text-gray-600 mt-1">
                        Showing: <span class="font-medium">My Assigned Statuses Only</span>
                    </p>
                @endif
            </div>
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

        <!-- User Assigned Statuses Info -->
        @if(!empty($userAssignedStatusIds))
            <div class="bg-purple-50 border-l-4 border-purple-400 px-4 py-3 mb-6 rounded">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-purple-900">Your Assigned Review Statuses</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($reviewStatuses->whereIn('id', $userAssignedStatusIds) as $status)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $status->color }}20; color: {{ $status->color }};">
                                    <span class="inline-block w-2 h-2 rounded-full mr-1.5" style="background-color: {{ $status->color }};"></span>
                                    {{ $status->name }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-xs text-purple-700 mt-2">
                            You can see OBRs with these statuses (to work on) and OBRs you created (to track progress). 
                            You can only change status for OBRs with your assigned statuses.
                        </p>
                    </div>
                </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Review Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($obligationRequests as $obr)
                        <tr class="hover:bg-gray-50" data-obr-id="{{ $obr->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $obr->obr_number }}
                                @if($obr->created_by == auth()->id())
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" title="You created this OBR">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                        </svg>
                                        Mine
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $obr->department->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $obr->claimantPayee->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $obr->obligation_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">₱{{ number_format($obr->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($obr->reviewStatus)
                                    <select class="status-dropdown text-xs rounded-full px-3 py-1 border-0 font-medium focus:ring-2 focus:ring-offset-1 cursor-pointer"
                                            style="background-color: {{ $obr->reviewStatus->color }}20; color: {{ $obr->reviewStatus->color }};"
                                            data-obr-id="{{ $obr->id }}"
                                            data-current-status="{{ $obr->review_status_id }}">
                                        @foreach($reviewStatuses as $status)
                                            @php
                                                $canChangeToStatus = empty($userAssignedStatusIds) || in_array($status->id, $userAssignedStatusIds);
                                            @endphp
                                            <option value="{{ $status->id }}" 
                                                    {{ $obr->review_status_id == $status->id ? 'selected' : '' }}
                                                    {{ !$canChangeToStatus ? 'disabled' : '' }}
                                                    data-color="{{ $status->color }}">
                                                {{ $status->name }}{{ !$canChangeToStatus ? ' (Not Assigned)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="status-dropdown text-xs rounded-full px-3 py-1 border-0 font-medium focus:ring-2 focus:ring-offset-1 cursor-pointer bg-gray-100 text-gray-600"
                                            data-obr-id="{{ $obr->id }}"
                                            data-current-status="">
                                        <option value="">Select Status</option>
                                        @foreach($reviewStatuses as $status)
                                            @php
                                                $canChangeToStatus = empty($userAssignedStatusIds) || in_array($status->id, $userAssignedStatusIds);
                                            @endphp
                                            <option value="{{ $status->id }}" 
                                                    {{ !$canChangeToStatus ? 'disabled' : '' }}
                                                    data-color="{{ $status->color }}">
                                                {{ $status->name }}{{ !$canChangeToStatus ? ' (Not Assigned)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
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
                                <x-input-label value="Fiscal Year" />
                                <select name="fiscal_year_id" id="fiscalYearInput" class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2">
                                    <option value="">Select Fiscal Year</option>
                                    @foreach($fiscalYears as $fiscalYear)
                                        <option value="{{ $fiscalYear->id }}" {{ $fiscalYear->is_current ? 'selected' : '' }}>
                                            {{ $fiscalYear->year }}
                                            @if($fiscalYear->is_current) (Current) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Line Items</h3>
                            <div class="flex gap-2">
                                <button type="button" id="refreshBudgetBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm hidden" title="Refresh budget data">
                                    🔄 Refresh Budget
                                </button>
                                <button type="button" id="addItemBtn" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                    + Add Item
                                </button>
                            </div>
                        </div>
                        
                        <!-- Budget Summary -->
                        <div id="budgetSummary" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Total Allocated:</span>
                                    <span class="font-bold text-blue-700 ml-2" id="totalAllocated">₱0.00</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Already Obligated:</span>
                                    <span class="font-bold text-orange-600 ml-2" id="totalObligated">₱0.00</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Current Entry:</span>
                                    <span class="font-bold text-gray-900 ml-2" id="totalEntered">₱0.00</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Available:</span>
                                    <span class="font-bold ml-2" id="totalAvailable">₱0.00</span>
                                </div>
                            </div>
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
    
    // Helper function to populate account dropdown with budget data
    function populateAccountDropdown(accountSelect, accounts) {
        accountSelect.innerHTML = '<option value="">Select Account or Sub Account</option>';
        
        accounts.forEach(acc => {
            const mainOption = document.createElement('option');
            mainOption.value = `account_${acc.id}`;
            const amountText = acc.is_selectable ? ` [Avail: ₱${parseFloat(acc.available_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]` : '';
            mainOption.textContent = `${acc.code} - ${acc.description}${amountText}`;
            mainOption.disabled = !acc.is_selectable;
            mainOption.style.fontWeight = 'bold';
            mainOption.dataset.allocated = acc.allocated_amount;
            mainOption.dataset.obligated = acc.obligated_amount;
            mainOption.dataset.available = acc.available_amount;
            accountSelect.appendChild(mainOption);
            
            if (acc.sub_accounts && acc.sub_accounts.length > 0) {
                acc.sub_accounts.forEach(sub => {
                    const subOption = document.createElement('option');
                    subOption.value = `sub_account_${sub.id}`;
                    const subAmountText = ` [Avail: ₱${parseFloat(sub.available_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}]`;
                    subOption.textContent = `  ↳ ${sub.code} - ${sub.description}${subAmountText}`;
                    subOption.dataset.allocated = sub.allocated_amount;
                    subOption.dataset.obligated = sub.obligated_amount;
                    subOption.dataset.available = sub.available_amount;
                    accountSelect.appendChild(subOption);
                });
            }
        });
    }

    let dataTable = null;
    let lastUpdateTime = new Date().toISOString();
    let pollingInterval = null;

    $(document).ready(function() {
        @if($obligationRequests->count() > 0)
        dataTable = $('#obrTable').DataTable({
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
            order: [[0, 'desc']], // Sort by OBR number descending (newest first)
        });

        // Style DataTables elements
        $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-64');
        $('.dt-buttons').addClass('flex flex-wrap gap-2');

        // Setup real-time updates (Echo or polling fallback)
        setupRealTimeUpdates();
        @endif

        // Reopen modal if there are validation errors
        @if ($errors->any())
            document.getElementById('obrModal').classList.remove('hidden');
            // Scroll to top of modal to show errors
            document.querySelector('#obrModal .bg-white').scrollTop = 0;
        @endif
    });

    // Function to check for new obligations
    function checkForNewObligations() {
        fetch('/api/obligation-requests')
            .then(response => response.json())
            .then(data => {
                let hasNewData = false;
                
                data.forEach(obr => {
                    // Check if this obligation is newer than our last update
                    if (obr.created_at > lastUpdateTime || obr.updated_at > lastUpdateTime) {
                        hasNewData = true;
                        
                        // Check if row already exists
                        const existingRow = document.querySelector(`tr[data-obr-id="${obr.id}"]`);
                        
                        if (!existingRow) {
                            // Add new row
                            addNewObrRow(obr);
                        } else {
                            // Update existing row
                            updateObrRow(existingRow, obr);
                        }
                    }
                });
                
                if (hasNewData) {
                    lastUpdateTime = new Date().toISOString();
                    
                    // Show notification
                    showNotification('New obligation request(s) detected!');
                }
            })
            .catch(error => console.error('Error checking for new obligations:', error));
    }

    // Function to add new OBR row to table
    function addNewObrRow(obr) {
        const statusClass = obr.status === 'approved' ? 'bg-green-100 text-green-800' :
                           obr.status === 'rejected' ? 'bg-red-100 text-red-800' :
                           'bg-gray-100 text-gray-800';
        
        const newRow = `
            <tr class="hover:bg-gray-50 bg-yellow-50 transition-colors duration-1000" data-obr-id="${obr.id}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${obr.obr_number}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${obr.department_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${obr.claimant_payee_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${obr.obligation_date}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">₱${parseFloat(obr.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full ${statusClass}">
                        ${obr.status.charAt(0).toUpperCase() + obr.status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex gap-2">
                        <a href="/obligation-requests/${obr.id}" 
                           class="text-blue-600 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50"
                           title="View">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <button class="editBtn text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50"
                            data-id="${obr.id}"
                            title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <form action="/obligation-requests/${obr.id}" method="POST" class="inline-block">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <input type="hidden" name="_method" value="DELETE">
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
        `;
        
        if (dataTable) {
            // Add to DataTable
            const rowNode = dataTable.row.add($(newRow)).draw(false).node();
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                $(rowNode).removeClass('bg-yellow-50');
            }, 3000);
            
            // Reattach edit button event listeners
            attachEditButtonListeners();
        }
    }

    // Function to update existing OBR row
    function updateObrRow(row, obr) {
        // Update the row data
        row.querySelector('td:nth-child(1)').textContent = obr.obr_number;
        row.querySelector('td:nth-child(2)').textContent = obr.department_name;
        row.querySelector('td:nth-child(3)').textContent = obr.claimant_payee_name;
        row.querySelector('td:nth-child(4)').textContent = obr.obligation_date;
        row.querySelector('td:nth-child(5)').textContent = `₱${parseFloat(obr.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        const statusClass = obr.status === 'approved' ? 'bg-green-100 text-green-800' :
                           obr.status === 'rejected' ? 'bg-red-100 text-red-800' :
                           'bg-gray-100 text-gray-800';
        
        const statusSpan = row.querySelector('td:nth-child(6) span');
        statusSpan.className = `px-2 py-1 text-xs rounded-full ${statusClass}`;
        statusSpan.textContent = obr.status.charAt(0).toUpperCase() + obr.status.slice(1);
        
        // Highlight updated row with blue
        row.classList.add('bg-blue-50');
        setTimeout(() => {
            row.classList.remove('bg-blue-50');
        }, 3000);
        
        // Update DataTable if it exists
        if (dataTable) {
            dataTable.row(row).invalidate().draw(false);
        }
    }

    // Function to remove OBR row
    function removeObrRow(row, obrNumber) {
        // Add fade-out animation
        row.classList.add('bg-red-50');
        row.style.transition = 'opacity 0.5s';
        row.style.opacity = '0';
        
        setTimeout(() => {
            if (dataTable) {
                // Remove from DataTable
                dataTable.row(row).remove().draw(false);
            } else {
                // Remove from DOM
                row.remove();
            }
        }, 500);
    }

    // Function to show notification
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        const bgColor = type === 'info' ? 'bg-blue-500' : type === 'error' ? 'bg-red-500' : 'bg-green-500';
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Remove after 3 seconds (or 5 seconds for info)
        const duration = type === 'info' ? 5000 : 3000;
        setTimeout(() => {
            notification.classList.add('animate-fade-out');
            setTimeout(() => notification.remove(), 500);
        }, duration);
    }

    // Setup real-time updates (Echo or polling fallback)
    function setupRealTimeUpdates() {
        // Check if Laravel Echo is available
        if (typeof window.Echo !== 'undefined') {
            console.log('✅ Using Laravel Echo for real-time updates');
            console.log('📡 Connecting to Pusher...');
            
            // Listen for obligation request events
            window.Echo.channel('obligation-requests')
                .listen('.obligation.created', (e) => {
                    console.log('🔔 New obligation received via broadcast:', e);
                    
                    // Check if this obligation already exists in the table
                    const existingRow = document.querySelector(`tr[data-obr-id="${e.id}"]`);
                    if (existingRow) {
                        console.log('⚠️ Obligation already exists in table, skipping duplicate');
                        return;
                    }
                    
                    addNewObrRow(e);
                    showNotification('New obligation request created by another user!');
                })
                .listen('.obligation.updated', (e) => {
                    console.log('✏️ Obligation updated via broadcast:', e);
                    
                    const existingRow = document.querySelector(`tr[data-obr-id="${e.id}"]`);
                    if (existingRow) {
                        updateObrRow(existingRow, e);
                        showNotification(`Obligation ${e.obr_number} updated by another user!`, 'info');
                    } else {
                        // If row doesn't exist, add it (edge case)
                        addNewObrRow(e);
                    }
                })
                .listen('.obligation.deleted', (e) => {
                    console.log('🗑️ Obligation deleted via broadcast:', e);
                    
                    const existingRow = document.querySelector(`tr[data-obr-id="${e.id}"]`);
                    if (existingRow) {
                        removeObrRow(existingRow, e.obr_number);
                        showNotification(`Obligation ${e.obr_number} deleted by another user!`, 'error');
                    }
                })
                .subscribed(() => {
                    console.log('✅ Successfully subscribed to obligation-requests channel');
                    showNotification('Real-time updates active', 'info');
                })
                .error((error) => {
                    console.error('❌ Error subscribing to channel:', error);
                });
                
            // Log Pusher connection state
            if (window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('✅ Pusher connected successfully');
                });
                
                window.Echo.connector.pusher.connection.bind('error', (err) => {
                    console.error('❌ Pusher connection error:', err);
                });
            }
        } else {
            console.log('⚠️ Laravel Echo not available, using polling fallback');
            startPolling();
        }
    }

    // Start polling (fallback when Echo is not available)
    function startPolling() {
        // Poll every 10 seconds
        pollingInterval = setInterval(checkForNewObligations, 10000);
    }

    // Stop polling (useful when modal is open)
    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    // Get all DOM elements first
    const addBtn = document.getElementById('addObrBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeModal = document.getElementById('closeModal');
    const modal = document.getElementById('obrModal');
    const formElement = document.getElementById('formElement');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const addItemBtn = document.getElementById('addItemBtn');
    const itemsContainer = document.getElementById('itemsContainer');

    // Pause polling when modal is open, resume when closed (only if using polling)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                // Only manage polling if Echo is not available
                if (typeof window.Echo === 'undefined') {
                    if (modal.classList.contains('hidden')) {
                        startPolling();
                    } else {
                        stopPolling();
                    }
                }
            }
        });
    });
    observer.observe(modal, { attributes: true });

    // Function to reattach edit button listeners after adding new rows
    function attachEditButtonListeners() {
        document.querySelectorAll('.editBtn').forEach(button => {
            // Remove old listeners by cloning
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Add new listener
            newButton.addEventListener('click', handleEditButtonClick);
        });
    }

    // Form submit validation
    formElement.addEventListener('submit', function(e) {
        // Check if any item exceeds available budget
        let hasOverBudget = false;
        let overBudgetItems = [];
        
        document.querySelectorAll('.item-row').forEach((row, index) => {
            const availableAmount = parseFloat(row.dataset.availableAmount) || 0;
            const enteredAmount = parseFloat(row.querySelector('.amount-input').value) || 0;
            
            if (availableAmount > 0 && enteredAmount > availableAmount) {
                hasOverBudget = true;
                overBudgetItems.push(index + 1);
            }
        });
        
        if (hasOverBudget) {
            e.preventDefault();
            alert(`Cannot submit: Item(s) ${overBudgetItems.join(', ')} exceed available budget.\n\nThis may happen if another user created an obligation while you were working.\n\nPlease refresh the page to see updated budget availability.`);
            return false;
        }
    });

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

    // Edit buttons - attach initial listeners
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', handleEditButtonClick);
    });

    // Edit button handler (extracted for reuse)
    async function handleEditButtonClick() {
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
                document.getElementById('fiscalYearInput').value = data.fiscal_year_id || '';
                
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
                        const fiscalYearId = document.getElementById('fiscalYearInput').value;
                        const accRes = await fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${item.department_id}/expense-types/${item.expense_type_id}/accounts?fiscal_year_id=${fiscalYearId}`);
                        const accounts = await accRes.json();
                        const accSelect = row.querySelector('.account-select');
                        populateAccountDropdown(accSelect, accounts);
                        
                        // Set selected account or sub-account
                        if (item.sub_account_id) {
                            accSelect.value = `sub_account_${item.sub_account_id}`;
                            row.querySelector('.sub-account-id-input').value = item.sub_account_id;
                        } else if (item.account_id) {
                            accSelect.value = `account_${item.account_id}`;
                            row.querySelector('.account-id-input').value = item.account_id;
                        }
                        
                        // Extract and store amounts from selected option
                        const selectedOption = accSelect.options[accSelect.selectedIndex];
                        if (selectedOption && selectedOption.dataset.allocated) {
                            const allocatedAmount = parseFloat(selectedOption.dataset.allocated);
                            const obligatedAmount = parseFloat(selectedOption.dataset.obligated);
                            const availableAmount = parseFloat(selectedOption.dataset.available);
                            
                            row.dataset.allocatedAmount = allocatedAmount;
                            row.dataset.obligatedAmount = obligatedAmount;
                            row.dataset.availableAmount = availableAmount;
                            
                            // Show budget info
                            const budgetInfo = row.querySelector('.item-budget-info');
                            budgetInfo.classList.remove('hidden');
                            row.querySelector('.item-allocated').textContent = `₱${allocatedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                            row.querySelector('.item-obligated').textContent = `₱${obligatedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                            row.querySelector('.item-available').textContent = `₱${availableAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                        }
                    }
                    
                    // Set amount
                    row.querySelector('.amount-input').value = item.amount;
                }
                
                // Update budget calculations after all items are loaded
                updateBudgetCalculations();
                
            } catch (error) {
                console.error('Error loading OBR data:', error);
                alert('Error loading obligation request data');
            }
    }

    // Cancel / Close modal
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));

    // Add Item
    addItemBtn.addEventListener('click', addItem);

    // Refresh Budget Button
    const refreshBudgetBtn = document.getElementById('refreshBudgetBtn');
    refreshBudgetBtn.addEventListener('click', async function() {
        const fundTypeId = document.getElementById('fundTypeInput').value;
        const fiscalYearId = document.getElementById('fiscalYearInput').value;
        
        if (!fundTypeId) {
            alert('Please select a fund type first.');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.textContent = '⏳ Refreshing...';
        
        try {
            // Reload accounts for each item that has expense type selected
            const refreshPromises = [];
            
            document.querySelectorAll('.item-row').forEach(row => {
                const deptId = row.querySelector('.department-select').value;
                const expenseTypeId = row.querySelector('.expense-type-select').value;
                const accountSelect = row.querySelector('.account-select');
                const currentValue = accountSelect.value; // Remember selection
                
                if (deptId && expenseTypeId) {
                    const promise = fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${deptId}/expense-types/${expenseTypeId}/accounts?year=${budgetYear}`)
                        .then(res => res.json())
                        .then(data => {
                            populateAccountDropdown(accountSelect, data);
                            
                            // Try to restore previous selection
                            if (currentValue) {
                                accountSelect.value = currentValue;
                                
                                // Update budget info with new data
                                const selectedOption = accountSelect.options[accountSelect.selectedIndex];
                                if (selectedOption && selectedOption.dataset.allocated) {
                                    const allocatedAmount = parseFloat(selectedOption.dataset.allocated);
                                    const obligatedAmount = parseFloat(selectedOption.dataset.obligated);
                                    const availableAmount = parseFloat(selectedOption.dataset.available);
                                    
                                    row.dataset.allocatedAmount = allocatedAmount;
                                    row.dataset.obligatedAmount = obligatedAmount;
                                    row.dataset.availableAmount = availableAmount;
                                    
                                    row.querySelector('.item-allocated').textContent = `₱${allocatedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                    row.querySelector('.item-obligated').textContent = `₱${obligatedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                    row.querySelector('.item-available').textContent = `₱${availableAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                }
                            }
                        });
                    refreshPromises.push(promise);
                }
            });
            
            await Promise.all(refreshPromises);
            
            // Update calculations with fresh data
            updateBudgetCalculations();
            
            // Show success message
            alert('Budget data refreshed successfully!');
            
        } catch (error) {
            console.error('Error refreshing budget:', error);
            alert('Error refreshing budget data. Please try again.');
        } finally {
            // Restore button state
            this.disabled = false;
            this.textContent = '🔄 Refresh Budget';
        }
    });

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
                        <input type="number" name="items[${itemIndex}][amount]" step="0.01" min="0.01" class="amount-input w-full border border-gray-300 rounded-lg px-3 py-2" required>
                        <div class="item-budget-info mt-1 text-xs hidden">
                            <span class="text-gray-600">Allocated: </span>
                            <span class="item-allocated font-semibold text-blue-600">₱0.00</span>
                            <span class="text-gray-600 ml-2">| Obligated: </span>
                            <span class="item-obligated font-semibold text-orange-600">₱0.00</span>
                            <span class="text-gray-600 ml-2">| Available: </span>
                            <span class="item-available font-semibold">₱0.00</span>
                        </div>
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

    // Fiscal Year change -> Reload accounts for all items that have expense type selected
    const fiscalYearInput = document.getElementById('fiscalYearInput');
    fiscalYearInput.addEventListener('change', function() {
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
                        populateAccountDropdown(accountSelect, data);
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
                const fiscalYearId = document.getElementById('fiscalYearInput').value;
                fetch(`/api/obligation-requests/fund-types/${fundTypeId}/departments/${deptId}/expense-types/${expenseTypeId}/accounts?fiscal_year_id=${fiscalYearId}`)
                    .then(res => res.json())
                    .then(data => {
                        populateAccountDropdown(accountSelect, data);
                    });
            } else {
                accountSelect.innerHTML = '<option value="">Select Account or Sub Account</option>';
            }
        }

        // Account/Sub Account selection -> Update hidden fields and store allocated amount
        if (e.target.classList.contains('account-select')) {
            const row = e.target.closest('.item-row');
            const selectedValue = e.target.value;
            const accountIdInput = row.querySelector('.account-id-input');
            const subAccountIdInput = row.querySelector('.sub-account-id-input');
            const selectedOption = e.target.options[e.target.selectedIndex];
            
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
                
                // Extract amounts from option data attributes
                if (selectedOption.dataset.allocated) {
                    const allocatedAmount = parseFloat(selectedOption.dataset.allocated);
                    const obligatedAmount = parseFloat(selectedOption.dataset.obligated);
                    const availableAmount = parseFloat(selectedOption.dataset.available);
                    
                    row.dataset.allocatedAmount = allocatedAmount;
                    row.dataset.obligatedAmount = obligatedAmount;
                    row.dataset.availableAmount = availableAmount;
                    
                    // Show budget info for this item
                    const budgetInfo = row.querySelector('.item-budget-info');
                    budgetInfo.classList.remove('hidden');
                    row.querySelector('.item-allocated').textContent = `₱${allocatedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    row.querySelector('.item-obligated').textContent = `₱${obligatedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    row.querySelector('.item-available').textContent = `₱${availableAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    
                    // Update calculations
                    updateBudgetCalculations();
                }
            } else {
                // Hide budget info if no account selected
                row.querySelector('.item-budget-info').classList.add('hidden');
                delete row.dataset.allocatedAmount;
                delete row.dataset.obligatedAmount;
                delete row.dataset.availableAmount;
                updateBudgetCalculations();
            }
        }
        
        // Amount input change -> Update calculations
        if (e.target.classList.contains('amount-input')) {
            updateBudgetCalculations();
        }
    });

    // Function to update all budget calculations
    function updateBudgetCalculations() {
        let totalAllocated = 0;
        let totalObligated = 0;
        let totalEntered = 0;
        let hasAllocations = false;
        let hasOverBudget = false;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const allocatedAmount = parseFloat(row.dataset.allocatedAmount) || 0;
            const obligatedAmount = parseFloat(row.dataset.obligatedAmount) || 0;
            const availableAmount = parseFloat(row.dataset.availableAmount) || 0;
            const enteredAmount = parseFloat(row.querySelector('.amount-input').value) || 0;
            
            if (allocatedAmount > 0) {
                hasAllocations = true;
                totalAllocated += allocatedAmount;
                totalObligated += obligatedAmount;
                
                // Update item-level available after current entry
                const remainingAfterEntry = availableAmount - enteredAmount;
                const itemAvailable = row.querySelector('.item-available');
                itemAvailable.textContent = `₱${remainingAfterEntry.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                
                // Color code: red if over budget, green if within
                if (remainingAfterEntry < 0) {
                    itemAvailable.classList.remove('text-green-600');
                    itemAvailable.classList.add('text-red-600');
                    hasOverBudget = true;
                } else {
                    itemAvailable.classList.remove('text-red-600');
                    itemAvailable.classList.add('text-green-600');
                }
            }
            
            totalEntered += enteredAmount;
        });
        
        // Show/hide budget summary and refresh button
        const budgetSummary = document.getElementById('budgetSummary');
        const refreshBudgetBtn = document.getElementById('refreshBudgetBtn');
        if (hasAllocations) {
            budgetSummary.classList.remove('hidden');
            refreshBudgetBtn.classList.remove('hidden');
            
            // Update summary values
            document.getElementById('totalAllocated').textContent = 
                `₱${totalAllocated.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('totalObligated').textContent = 
                `₱${totalObligated.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            document.getElementById('totalEntered').textContent = 
                `₱${totalEntered.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            
            const totalAvailable = totalAllocated - totalObligated - totalEntered;
            const availableElement = document.getElementById('totalAvailable');
            availableElement.textContent = 
                `₱${totalAvailable.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            
            // Color code total available
            if (totalAvailable < 0) {
                availableElement.classList.remove('text-green-600');
                availableElement.classList.add('text-red-600');
            } else {
                availableElement.classList.remove('text-red-600');
                availableElement.classList.add('text-green-600');
            }
            
            // Disable/enable submit button based on budget
            const submitBtn = document.getElementById('submitBtn');
            if (hasOverBudget) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.title = 'Cannot submit: Amount exceeds available budget';
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.title = '';
            }
        } else {
            budgetSummary.classList.add('hidden');
            refreshBudgetBtn.classList.add('hidden');
        }
    }

    // Handle status dropdown change
    $(document).on('change', '.status-dropdown', function() {
        const dropdown = $(this);
        const obrId = dropdown.data('obr-id');
        const newStatusId = dropdown.val();
        const currentStatusId = dropdown.data('current-status');
        const selectedOption = dropdown.find('option:selected');
        const newColor = selectedOption.data('color');

        if (!newStatusId) {
            return;
        }

        // Confirm status change
        if (!confirm('Are you sure you want to change the review status?')) {
            dropdown.val(currentStatusId);
            return;
        }

        // Show loading state
        dropdown.prop('disabled', true);

        // Send AJAX request to update status
        $.ajax({
            url: `/obligation-requests/${obrId}/update-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                review_status_id: newStatusId
            },
            success: function(response) {
                console.log('✅ Status updated successfully:', response);
                
                // Update dropdown styling
                dropdown.css({
                    'background-color': newColor + '20',
                    'color': newColor
                });
                dropdown.data('current-status', newStatusId);
                
                // Show success notification
                showNotification('Status updated successfully!', 'success');
                
                dropdown.prop('disabled', false);
            },
            error: function(xhr) {
                console.error('❌ Error updating status:', xhr);
                
                // Revert dropdown
                dropdown.val(currentStatusId);
                dropdown.prop('disabled', false);
                
                // Show error notification
                const errorMsg = xhr.responseJSON?.message || 'Failed to update status';
                showNotification(errorMsg, 'error');
            }
        });
    });

    // Notification helper
    function showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const notification = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
                ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.addClass('animate-fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    </script>
    @endpush

</x-app-layout>
