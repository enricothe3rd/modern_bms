<x-app-layout>
<x-dashboard-header
    title="Supplemental Budgets"
    subtitle="Manage supplemental budget allocations by year and group."
/>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <x-primary-button onclick="window.location.href='{{ route('supplemental-budgets.create') }}'">
            Create New Supplemental Budget
        </x-primary-button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('supplemental-budgets.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-input-label for="fiscal_year_id" value="Fiscal Year" />
                <select name="fiscal_year_id" id="fiscal_year_id" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1">
                    <option value="">All Fiscal Years</option>
                    @foreach($fiscalYears as $fiscalYear)
                        <option value="{{ $fiscalYear->id }}" {{ request('fiscal_year_id') == $fiscalYear->id ? 'selected' : '' }}>
                            {{ $fiscalYear->year }}
                            @if($fiscalYear->is_current) (Current) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="group" value="Supplemental Group" />
                <select name="group" id="group" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                            {{ $group }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <x-primary-button type="submit">
                    Filter
                </x-primary-button>
                <x-secondary-button onclick="window.location.href='{{ route('supplemental-budgets.index') }}'">
                    Clear
                </x-secondary-button>
            </div>
        </form>
    </div>

    <!-- Supplemental Budgets Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiscal Year</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($supplementalBudgets as $budget)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $budget->title }}</div>
                            @if($budget->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($budget->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $budget->fiscalYear->year ?? 'N/A' }}
                            @if($budget->fiscalYear && $budget->fiscalYear->is_current)
                                <span class="text-xs text-green-600">(Current)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $budget->supplemental_group }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($budget->status === 'approved') bg-green-100 text-green-800
                                @elseif($budget->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($budget->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $budget->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 currency">
                            {{ number_format($budget->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $budget->creator->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- View -->
                                <a href="{{ route('supplemental-budgets.show', $budget) }}" 
                                   class="text-blue-600 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50"
                                   title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                @if($budget->status === 'draft')
                                    <!-- Edit -->
                                    <a href="{{ route('supplemental-budgets.edit', $budget) }}" 
                                       class="text-yellow-600 hover:text-yellow-700 p-1 rounded-full hover:bg-yellow-50"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete -->
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50 delete-btn"
                                            title="Delete"
                                            data-url="{{ route('supplemental-budgets.destroy', $budget) }}"
                                            data-title="{{ $budget->title }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No supplemental budgets found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $supplementalBudgets->links() }}
    </div>
</div>

<!-- Confirmation Modal -->
<x-confirmation-modal 
    id="confirmationModal"
    title="Confirm Action"
    message="Are you sure you want to proceed?"
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="bg-red-600 hover:bg-red-700"
    icon="warning"
/>

<style>
.currency::before {
    content: "₱ ";
    font-weight: bold;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.url;
            const title = this.dataset.title;
            
            showConfirmation({
                title: 'Delete Supplemental Budget',
                message: `Are you sure you want to delete "${title}"? This action cannot be undone.`,
                onConfirm: () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
</x-app-layout>