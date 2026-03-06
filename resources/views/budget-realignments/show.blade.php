<x-app-layout>
    @section('title', 'Budget Realignment Details')

    <x-dashboard-header
        title="Budget Realignment Details"
        subtitle="View budget realignment information and items"
    />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900">{{ $budgetRealignment->realignment_number }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $budgetRealignment->description }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('budget-realignments.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                    ← Back to List
                </a>
                
                @if($budgetRealignment->canEdit())
                    <a href="{{ route('budget-realignments.edit', $budgetRealignment) }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-md transition">
                        Edit
                    </a>
                @endif
                
                @if($budgetRealignment->canSubmit())
                    <form action="{{ route('budget-realignments.submit', $budgetRealignment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition"
                                onclick="return confirm('Are you sure you want to submit this realignment for approval?')">
                            Submit for Approval
                        </button>
                    </form>
                @endif
                
                @if($budgetRealignment->canApprove())
                    <button type="button" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition"
                            onclick="showApprovalModal('approve')">
                        Approve
                    </button>
                    <button type="button" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition"
                            onclick="showApprovalModal('reject')">
                        Reject
                    </button>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Basic Information -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Realignment Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->realignment_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fiscal Year</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $budgetRealignment->fiscalYear->year ?? 'N/A' }}
                            @if($budgetRealignment->fiscalYear && $budgetRealignment->fiscalYear->is_current)
                                <span class="text-xs text-green-600">(Current)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <p class="mt-1 text-sm text-gray-900 font-semibold">₱{{ number_format($budgetRealignment->total_amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-1"
                              style="background-color: {{ $budgetRealignment->status_color }}20; color: {{ $budgetRealignment->status_color }};">
                            <span class="inline-block w-2 h-2 rounded-full mr-1.5" 
                                  style="background-color: {{ $budgetRealignment->status_color }};"></span>
                            {{ $budgetRealignment->status_label }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created By</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->creator->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @if($budgetRealignment->approved_by)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $budgetRealignment->status === 'approved' ? 'Approved' : 'Rejected' }} By</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->approver->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $budgetRealignment->status === 'approved' ? 'Approved' : 'Rejected' }} Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->approved_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
                @if($budgetRealignment->remarks)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Remarks</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->remarks }}</p>
                    </div>
                @endif
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $budgetRealignment->description }}</p>
                </div>
            </div>
        </div>

        <!-- From Items (Sources) -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-800">From (Source Allocations)</h3>
                <p class="text-sm text-red-600 mt-1">Budget transferred FROM these allocations</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expense Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($budgetRealignment->fromItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->allocation->department->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->allocation->expenseType->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->allocation->account->description ?? 'N/A' }}
                                    @if($item->allocation->subAccount)
                                        <br><span class="text-xs text-gray-500">{{ $item->allocation->subAccount->description }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">
                                    -₱{{ number_format($item->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->remarks ?: 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-red-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-red-800">Total From:</td>
                            <td class="px-6 py-3 text-sm font-bold text-red-800">-₱{{ number_format($budgetRealignment->fromItems->sum('amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- To Items (Destinations) -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                <h3 class="text-lg font-semibold text-green-800">To (Destination Allocations)</h3>
                <p class="text-sm text-green-600 mt-1">Budget transferred TO these allocations</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expense Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($budgetRealignment->toItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->allocation->department->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->allocation->expenseType->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->allocation->account->description ?? 'N/A' }}
                                    @if($item->allocation->subAccount)
                                        <br><span class="text-xs text-gray-500">{{ $item->allocation->subAccount->description }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                                    +₱{{ number_format($item->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->remarks ?: 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-green-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-green-800">Total To:</td>
                            <td class="px-6 py-3 text-sm font-bold text-green-800">+₱{{ number_format($budgetRealignment->toItems->sum('amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    @if($budgetRealignment->canApprove())
        <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Approve Realignment</h3>
                    <form action="{{ route('budget-realignments.approve', $budgetRealignment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" id="modalAction" value="approve">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                            <textarea name="remarks" rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                      placeholder="Optional remarks..."></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="hideApprovalModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" id="modalSubmitBtn"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Approve
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
    function showApprovalModal(action) {
        const modal = document.getElementById('approvalModal');
        const title = document.getElementById('modalTitle');
        const actionInput = document.getElementById('modalAction');
        const submitBtn = document.getElementById('modalSubmitBtn');
        
        if (action === 'approve') {
            title.textContent = 'Approve Realignment';
            actionInput.value = 'approve';
            submitBtn.textContent = 'Approve';
            submitBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
        } else {
            title.textContent = 'Reject Realignment';
            actionInput.value = 'reject';
            submitBtn.textContent = 'Reject';
            submitBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
        }
        
        modal.classList.remove('hidden');
    }
    
    function hideApprovalModal() {
        document.getElementById('approvalModal').classList.add('hidden');
    }
    </script>
    @endpush
</x-app-layout>