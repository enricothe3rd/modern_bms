<x-app-layout>
    @section('title', 'Budget Realignments')

    <x-dashboard-header
        title="Budget Realignments"
        subtitle="Manage budget transfers between allocations"
    />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900">Budget Realignments</h2>
                <p class="text-sm text-gray-600 mt-1">Transfer budget allocations between departments and expense types</p>
            </div>
            <a href="{{ route('budget-realignments.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                + Create Realignment
            </a>
        </div>

        <!-- Fiscal Year Filter -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('budget-realignments.index') }}" class="flex items-end gap-4">
                    <div class="flex-1">
                        <x-input-label value="Filter by Fiscal Year" />
                        <select name="fiscal_year_id" 
                                class="mt-1 block w-full border border-neutral-300 rounded-lg px-4 py-2"
                                onchange="this.form.submit()">
                            <option value="">All Fiscal Years</option>
                            @foreach($fiscalYears as $fiscalYear)
                                <option value="{{ $fiscalYear->id }}" 
                                        {{ $fiscalYearId == $fiscalYear->id ? 'selected' : '' }}>
                                    {{ $fiscalYear->year }} 
                                    @if($fiscalYear->is_current) (Current) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                            Filter
                        </button>
                    </div>
                    @if($fiscalYearId)
                        <div>
                            <a href="{{ route('budget-realignments.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                                Clear
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Realignments Table -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Realignment Number
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fiscal Year
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($realignments as $realignment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $realignment->realignment_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="max-w-xs truncate" title="{{ $realignment->description }}">
                                        {{ $realignment->description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $realignment->fiscalYear->year ?? 'N/A' }}
                                    @if($realignment->fiscalYear && $realignment->fiscalYear->is_current)
                                        <span class="text-xs text-green-600">(Current)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    ₱{{ number_format($realignment->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                          style="background-color: {{ $realignment->status_color }}20; color: {{ $realignment->status_color }};">
                                        <span class="inline-block w-2 h-2 rounded-full mr-1.5" 
                                              style="background-color: {{ $realignment->status_color }};"></span>
                                        {{ $realignment->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $realignment->creator->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $realignment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('budget-realignments.show', $realignment) }}" 
                                           class="text-blue-600 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50"
                                           title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        @if($realignment->canEdit())
                                            <a href="{{ route('budget-realignments.edit', $realignment) }}" 
                                               class="text-yellow-500 hover:text-yellow-600 p-1 rounded-full hover:bg-yellow-50"
                                               title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            
                                            <form action="{{ route('budget-realignments.destroy', $realignment) }}" 
                                                  method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-700 p-1 rounded-full hover:bg-red-50"
                                                    onclick="return confirm('Are you sure you want to delete this realignment?')"
                                                    title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No budget realignments found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($realignments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $realignments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>