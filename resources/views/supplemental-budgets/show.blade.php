<x-app-layout>
<x-dashboard-header
    title="{{ $supplementalBudget->title }}"
    subtitle="View supplemental budget details and manage approval status."
/>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <div class="flex space-x-2">
            <x-secondary-button onclick="window.location.href='{{ route('supplemental-budgets.index') }}'">
                Back to List
            </x-secondary-button>
            
            @if($supplementalBudget->status === 'draft')
                <x-secondary-button onclick="window.location.href='{{ route('supplemental-budgets.edit', $supplementalBudget) }}'">
                    Edit
                </x-secondary-button>
                
                <x-primary-button 
                    class="submit-btn"
                    data-url="{{ route('supplemental-budgets.submit', $supplementalBudget) }}"
                    data-action="submit">
                    Submit for Approval
                </x-primary-button>
            @endif

            @if($supplementalBudget->status === 'pending')
                <x-primary-button onclick="showApprovalModal()">
                    Approve
                </x-primary-button>
                
                <x-danger-button onclick="showRejectionModal()">
                    Reject
                </x-danger-button>
            @endif
        </div>
    </div>

    <!-- Basic Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->title }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fiscal Year</label>
                <p class="mt-1 text-sm text-gray-900">
                    {{ $supplementalBudget->fiscalYear->year ?? 'N/A' }}
                    @if($supplementalBudget->fiscalYear && $supplementalBudget->fiscalYear->is_current)
                        <span class="text-xs text-green-600">(Current)</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Supplemental Group</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->supplemental_group }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($supplementalBudget->status === 'approved') bg-green-100 text-green-800
                    @elseif($supplementalBudget->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($supplementalBudget->status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $supplementalBudget->status_label }}
                </span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                <p class="mt-1 text-sm text-gray-900 currency">{{ number_format($supplementalBudget->total_amount, 2) }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Created By</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->creator->name ?? 'N/A' }}</p>
            </div>

            @if($supplementalBudget->submission_date)
            <div>
                <label class="block text-sm font-medium text-gray-700">Submission Date</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->submission_date->format('M d, Y') }}</p>
            </div>
            @endif

            @if($supplementalBudget->approval_date)
            <div>
                <label class="block text-sm font-medium text-gray-700">Approval Date</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->approval_date->format('M d, Y') }}</p>
            </div>
            @endif

            @if($supplementalBudget->approver)
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ $supplementalBudget->status === 'approved' ? 'Approved By' : 'Reviewed By' }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->approver->name }}</p>
            </div>
            @endif
        </div>

        @if($supplementalBudget->description)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->description }}</p>
        </div>
        @endif

        @if($supplementalBudget->remarks)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Remarks</label>
            <p class="mt-1 text-sm text-gray-900">{{ $supplementalBudget->remarks }}</p>
        </div>
        @endif
    </div>

    <!-- Budget Items -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Budget Items</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expense Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justification</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplementalBudget->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->expenseType->description ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->account->description ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->subAccount->description ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 currency">
                                {{ number_format($item->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $item->justification ?? 'N/A' }}
                                @if($item->remarks)
                                    <br><small class="text-gray-500">{{ $item->remarks }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total:</td>
                        <td class="px-6 py-3 text-sm font-bold text-gray-900 currency">{{ number_format($supplementalBudget->total_amount, 2) }}</td>
                        <td class="px-6 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Approve Supplemental Budget</h3>
            <form action="{{ route('supplemental-budgets.approve', $supplementalBudget) }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="approval_remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
                    <textarea name="remarks" id="approval_remarks" rows="3" 
                              class="w-full border border-gray-300 rounded-md px-3 py-2"
                              placeholder="Optional approval remarks"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <x-secondary-button onclick="hideApprovalModal()">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Approve
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Reject Supplemental Budget</h3>
            <form action="{{ route('supplemental-budgets.reject', $supplementalBudget) }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="rejection_remarks" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
                    <textarea name="remarks" id="rejection_remarks" rows="3" 
                              class="w-full border border-gray-300 rounded-md px-3 py-2"
                              placeholder="Please provide reason for rejection"
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <x-secondary-button onclick="hideRejectionModal()">
                        Cancel
                    </x-secondary-button>
                    <x-danger-button type="submit">
                        Reject
                    </x-danger-button>
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
    confirm-class="bg-blue-600 hover:bg-blue-700"
    icon="info"
/>

<style>
.currency::before {
    content: "₱ ";
    font-weight: bold;
}
</style>

<script>
function showApprovalModal() {
    document.getElementById('approvalModal').classList.remove('hidden');
}

function hideApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
}

function showRejectionModal() {
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function hideRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle submit button
    const submitBtn = document.querySelector('.submit-btn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            const url = this.dataset.url;
            
            showConfirmation({
                title: 'Submit for Approval',
                message: 'Are you sure you want to submit this budget for approval?',
                onConfirm: () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    }
});
</script>
</x-app-layout>