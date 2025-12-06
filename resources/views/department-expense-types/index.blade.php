<x-app-layout>
    @section('title', 'Department Expense Types')

<x-dashboard-header
    title="Department Expense Types"
    subtitle="Manage expense types for {{ $department->name }}"
/>

<div class="max-w-6xl mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">{{ $department->name }} - Expense Types</h2>
            <p class="text-gray-600 mt-1">Department Code: <span class="font-semibold">{{ $department->code }}</span> | Sector: <span class="font-semibold">{{ $department->sector->name ?? 'No Sector' }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('departments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 border shadow transition">
                ← Back to Departments
            </a>
            <button id="assignExpenseTypesBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 border shadow transition">
                Assign Expense Types
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Current Assignments -->
    <div class="bg-white shadow border border-gray-200 p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Currently Assigned Expense Types</h3>
        
        @if($department->expenseTypes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($department->expenseTypes as $expenseType)
                    <div class="group relative bg-blue-50 border-2 border-blue-200 p-4 hover:shadow transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-600 border flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ $expenseType->acronym }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $expenseType->description }}</p>
                                        <p class="text-xs text-gray-500">{{ $expenseType->acronym }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1">
                                <!-- Budget Button -->
                                <a href="{{ route('department-expense-type-allocations.index', [$department->id, $expenseType->id]) }}" 
                                   class="opacity-0 group-hover:opacity-100 inline-flex items-center justify-center w-8 h-8 bg-green-600 hover:bg-green-700 text-white rounded-md shadow-sm transition-all duration-200 transform hover:scale-105"
                                   title="Manage Budget Allocations">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </a>
                                
                                <!-- PPA Button -->
                                <button type="button" 
                                    class="opacity-0 group-hover:opacity-100 inline-flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition-all duration-200 transform hover:scale-105 ppa-btn"
                                    data-department-id="{{ $department->id }}"
                                    data-expense-type-id="{{ $expenseType->id }}"
                                    data-ppa-code="{{ $expenseType->pivot->ppa_code ?? '' }}"
                                    data-date-issued="{{ $expenseType->pivot->date_issued ?? '' }}"
                                    data-status="{{ $expenseType->pivot->ppa_code ? ($expenseType->pivot->status ?? 'saved') : 'not_set' }}"
                                    title="Manage PPA">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Remove Button (Only for users with delete permission) -->
                                @if(auth()->check() && auth()->user()->canDeleteExpenseTypes())
                                    <form action="{{ route('department-expense-types.destroy', [$department->id, $expenseType->id]) }}" method="POST" class="inline remove-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                            class="opacity-0 group-hover:opacity-100 inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-md shadow-sm transition-all duration-200 transform hover:scale-105 remove-btn"
                                            data-expense-type="{{ $expenseType->description }}"
                                            title="Remove Expense Type">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Quick Budget Info -->
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            @php
                                $totalAllocated = \App\Models\DepartmentExpenseTypeAllocation::where('department_id', $department->id)
                                    ->where('expense_type_id', $expenseType->id)
                                    ->sum('amount');
                                $allocationCount = \App\Models\DepartmentExpenseTypeAllocation::where('department_id', $department->id)
                                    ->where('expense_type_id', $expenseType->id)
                                    ->count();
                            @endphp
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="text-gray-600">
                                    {{ $allocationCount }} {{ Str::plural('allocation', $allocationCount) }}
                                </span>
                                <span class="font-semibold text-green-600">
                                    ₱{{ number_format($totalAllocated, 2) }}
                                </span>
                            </div>
                            
                            <!-- PPA Info -->
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">
                                    PPA: {{ $expenseType->pivot->ppa_code ?? 'Not Set' }}
                                </span>
                                @if($expenseType->pivot->ppa_code)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ ($expenseType->pivot->status ?? 'saved') === 'released' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($expenseType->pivot->status ?? 'saved') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Not Set
                                    </span>
                                @endif
                            </div>
                            @if($expenseType->pivot->date_issued)
                                <div class="text-xs text-gray-500 mt-1">
                                    Issued: {{ \Carbon\Carbon::parse($expenseType->pivot->date_issued)->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No expense types assigned</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by assigning expense types to this department.</p>
                <div class="mt-6">
                    <button id="assignExpenseTypesBtnEmpty" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 border shadow transition">
                        Assign Expense Types
                    </button>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Assignment Modal -->
<div id="assignmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white border shadow-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
        
        <!-- Modal Header -->
        <div class="bg-blue-600 px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">Assign Expense Types to {{ $department->name }}</h2>
                <button id="closeModal" class="text-white hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
            <form id="assignmentForm" method="POST" action="{{ route('department-expense-types.store', $department->id) }}">
                @csrf
                
                <!-- Select All Controls -->
                <div class="mb-6 p-4 bg-gray-50 border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Select Expense Types</h3>
                        <div class="flex gap-2">
                            <button type="button" id="selectAllBtn" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 border hover:bg-blue-200 transition">
                                Select All
                            </button>
                            <button type="button" id="clearAllBtn" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 border hover:bg-gray-200 transition">
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Expense Types Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach($allExpenseTypes as $expenseType)
                        <label class="expense-type-item group cursor-pointer">
                            <input type="checkbox" 
                                name="expense_type_ids[]" 
                                value="{{ $expenseType->id }}"
                                class="expense-type-checkbox sr-only"
                                {{ in_array($expenseType->id, $assignedExpenseTypeIds) ? 'checked' : '' }}>
                            <div class="border-2 border-gray-200 p-4 transition-all duration-200 group-hover:border-blue-300 group-hover:shadow checkbox-container">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-gray-500 border flex items-center justify-center checkbox-icon">
                                            <span class="text-white font-bold text-sm">{{ $expenseType->acronym }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $expenseType->description }}</p>
                                        <p class="text-xs text-gray-500">{{ $expenseType->acronym }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 border-2 border-gray-300 checkbox-visual">
                                            <svg class="w-3 h-3 text-white hidden checkmark" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 border border-blue-600 shadow transition">
                        Assign Selected Expense Types
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<x-confirmation-modal 
    id="confirmationModal"
    title="Confirm Action"
    message="Are you sure you want to proceed?"
    confirm-text="Confirm"
    cancel-text="Cancel"
    confirm-class="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<!-- PPA Modal -->
<div id="ppaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
        
        <!-- Close Button -->
        <button id="closePpaModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-4" id="ppaModalTitle">Manage PPA Information</h2>

        <!-- Form -->
        <form id="ppaForm" method="POST">
            @csrf
            @method('PUT')

            <!-- PPA Code -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">PPA Code</label>
                <select id="ppaCodeSelect" name="ppa_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="">Select PPA Code</option>
                    @for($i = 100; $i <= 5000; $i += 100)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Date Issued -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Issued</label>
                <input type="date" id="dateIssuedInput" name="date_issued" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <!-- Status Display -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <span id="statusDisplay" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    Saved
                </span>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelPpaBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" id="savePpaBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                    Save
                </button>
                <button type="button" id="releasePpaBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition hidden">
                    Release
                </button>
                @if(auth()->check() && auth()->user()->canUnreleasedPpa())
                    <button type="button" id="unreleasePpaBtn" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md transition hidden">
                        Unreleased
                    </button>
                @endif
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assignmentModal');
    const assignBtn = document.getElementById('assignExpenseTypesBtn');
    const assignBtnEmpty = document.getElementById('assignExpenseTypesBtnEmpty');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');

    // Show modal
    function showModal() {
        modal.classList.remove('hidden');
        updateCheckboxStyles();
    }

    // Hide modal
    function hideModal() {
        modal.classList.add('hidden');
    }

    // Event listeners
    if (assignBtn) assignBtn.addEventListener('click', showModal);
    if (assignBtnEmpty) assignBtnEmpty.addEventListener('click', showModal);
    closeModal.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    // Select/Clear all functionality
    selectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.expense-type-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateCheckboxStyles();
    });

    clearAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.expense-type-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateCheckboxStyles();
    });

    // Checkbox styling and interaction
    document.querySelectorAll('.expense-type-item').forEach(item => {
        item.addEventListener('click', function() {
            const checkbox = this.querySelector('.expense-type-checkbox');
            checkbox.checked = !checkbox.checked;
            updateCheckboxStyles();
        });
    });

    function updateCheckboxStyles() {
        document.querySelectorAll('.expense-type-item').forEach(item => {
            const checkbox = item.querySelector('.expense-type-checkbox');
            const container = item.querySelector('.checkbox-container');
            const icon = item.querySelector('.checkbox-icon');
            const visual = item.querySelector('.checkbox-visual');
            const checkmark = item.querySelector('.checkmark');

            if (checkbox.checked) {
                container.classList.remove('border-gray-200');
                container.classList.add('border-blue-500', 'bg-blue-50');
                icon.classList.remove('bg-gray-500');
                icon.classList.add('bg-blue-600');
                visual.classList.remove('border-gray-300');
                visual.classList.add('border-blue-500', 'bg-blue-500');
                checkmark.classList.remove('hidden');
            } else {
                container.classList.remove('border-blue-500', 'bg-blue-50');
                container.classList.add('border-gray-200');
                icon.classList.remove('bg-blue-600');
                icon.classList.add('bg-gray-500');
                visual.classList.remove('border-blue-500', 'bg-blue-500');
                visual.classList.add('border-gray-300');
                checkmark.classList.add('hidden');
            }
        });
    }

    // Initialize checkbox styles
    updateCheckboxStyles();

    // PPA Modal functionality
    const ppaModal = document.getElementById('ppaModal');
    const closePpaModal = document.getElementById('closePpaModal');
    const cancelPpaBtn = document.getElementById('cancelPpaBtn');
    const ppaForm = document.getElementById('ppaForm');
    const ppaCodeSelect = document.getElementById('ppaCodeSelect');
    const dateIssuedInput = document.getElementById('dateIssuedInput');
    const statusDisplay = document.getElementById('statusDisplay');
    const savePpaBtn = document.getElementById('savePpaBtn');
    const releasePpaBtn = document.getElementById('releasePpaBtn');
    const unreleasePpaBtn = document.getElementById('unreleasePpaBtn');

    // Show PPA modal
    document.querySelectorAll('.ppa-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const departmentId = this.dataset.departmentId;
            const expenseTypeId = this.dataset.expenseTypeId;
            const ppaCode = this.dataset.ppaCode;
            const dateIssued = this.dataset.dateIssued;
            const status = this.dataset.status;

            // Set form action
            ppaForm.action = `/departments/${departmentId}/expense-types/${expenseTypeId}/ppa-info`;

            // Populate form
            ppaCodeSelect.value = ppaCode;
            dateIssuedInput.value = dateIssued;

            // Update status display
            if (status === 'released') {
                statusDisplay.textContent = 'Released';
                statusDisplay.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                savePpaBtn.style.display = 'none';
                releasePpaBtn.style.display = 'none';
                
                // Show unreleased button if user has permission
                if (unreleasePpaBtn) {
                    unreleasePpaBtn.style.display = 'inline-block';
                    unreleasePpaBtn.onclick = function() {
                        showConfirmation({
                            title: 'Unreleased PPA',
                            message: 'Are you sure you want to unreleased this PPA? This will change the status back to Saved.',
                            onConfirm: function() {
                                const unreleasedForm = document.createElement('form');
                                unreleasedForm.method = 'POST';
                                unreleasedForm.action = `/departments/${departmentId}/expense-types/${expenseTypeId}/unreleased`;
                                unreleasedForm.innerHTML = `
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="PUT">
                                `;
                                document.body.appendChild(unreleasedForm);
                                unreleasedForm.submit();
                            }
                        });
                    };
                } else if (unreleasePpaBtn) {
                    unreleasePpaBtn.style.display = 'none';
                }
            } else if (status === 'not_set') {
                statusDisplay.textContent = 'Not Set';
                statusDisplay.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600';
                savePpaBtn.style.display = 'inline-block';
                releasePpaBtn.style.display = 'none';
                if (unreleasePpaBtn) unreleasePpaBtn.style.display = 'none';
            } else {
                statusDisplay.textContent = 'Saved';
                statusDisplay.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
                savePpaBtn.style.display = 'inline-block';
                
                // Show release button only if PPA code and date are set
                if (ppaCode && dateIssued) {
                    releasePpaBtn.style.display = 'inline-block';
                    releasePpaBtn.onclick = function() {
                        showConfirmation({
                            title: 'Release PPA',
                            message: 'Are you sure you want to release this PPA? This action cannot be undone.',
                            onConfirm: function() {
                                const releaseForm = document.createElement('form');
                                releaseForm.method = 'POST';
                                releaseForm.action = `/departments/${departmentId}/expense-types/${expenseTypeId}/release`;
                                releaseForm.innerHTML = `
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="PUT">
                                `;
                                document.body.appendChild(releaseForm);
                                releaseForm.submit();
                            }
                        });
                    };
                } else {
                    releasePpaBtn.style.display = 'none';
                }
                if (unreleasePpaBtn) unreleasePpaBtn.style.display = 'none';
            }

            ppaModal.classList.remove('hidden');
        });
    });

    // Hide PPA modal
    function hidePpaModal() {
        ppaModal.classList.add('hidden');
    }

    closePpaModal.addEventListener('click', hidePpaModal);
    cancelPpaBtn.addEventListener('click', hidePpaModal);

    // Show/hide release button based on form completion
    function checkFormCompletion() {
        if (ppaCodeSelect.value && dateIssuedInput.value) {
            releasePpaBtn.style.display = 'inline-block';
        } else {
            releasePpaBtn.style.display = 'none';
        }
    }

    ppaCodeSelect.addEventListener('change', checkFormCompletion);
    dateIssuedInput.addEventListener('change', checkFormCompletion);

    // Handle remove button confirmations
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const expenseType = this.dataset.expenseType;
            const form = this.closest('.remove-form');
            
            showConfirmation({
                title: 'Remove Expense Type',
                message: `Are you sure you want to remove "${expenseType}" from this department? This will also remove all associated budget allocations.`,
                onConfirm: function() {
                    form.submit();
                }
            });
        });
    });
});
</script>

</x-app-layout>