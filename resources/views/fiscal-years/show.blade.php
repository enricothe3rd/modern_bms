<x-app-layout>
    @section('title', 'Fiscal Year ' . $fiscalYear->year)

    <x-dashboard-header
        title="Fiscal Year {{ $fiscalYear->year }}"
        subtitle="View fiscal year details and statistics"
    />

    <div class="max-w-4xl mx-auto p-6">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('fiscal-years.index') }}" class="text-blue-600 hover:text-blue-700">
                ← Back to Fiscal Years
            </a>
        </div>

        <!-- Fiscal Year Details -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $fiscalYear->year }}</h2>
                    <p class="text-gray-600">{{ $fiscalYear->description ?: 'Fiscal Year ' . $fiscalYear->year }}</p>
                </div>
                <div class="flex gap-2">
                    @if($fiscalYear->is_current)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Current
                        </span>
                    @endif
                    @if($fiscalYear->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Inactive
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Period</h3>
                    <p class="text-lg text-gray-900">
                        {{ $fiscalYear->start_date->format('F j, Y') }} - {{ $fiscalYear->end_date->format('F j, Y') }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $fiscalYear->start_date->diffInDays($fiscalYear->end_date) + 1 }} days
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Status</h3>
                    <div class="space-y-2">
                        @if($fiscalYear->is_current)
                            <div class="flex items-center text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Current fiscal year
                            </div>
                        @endif
                        @if($fiscalYear->is_active)
                            <div class="flex items-center text-green-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Active for budget planning
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-2 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('fiscal-years.edit', $fiscalYear) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-md transition">
                    Edit
                </a>
                @if(!$fiscalYear->is_current)
                    <form action="{{ route('fiscal-years.set-current', $fiscalYear) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition"
                            onclick="return confirm('Set this as the current fiscal year?')">
                            Set as Current
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Statistics (placeholder for future features) -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">0</div>
                    <div class="text-sm text-gray-600">Budget Allocations</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">₱0.00</div>
                    <div class="text-sm text-gray-600">Total Budget</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">0</div>
                    <div class="text-sm text-gray-600">Obligation Requests</div>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-4 text-center">
                Statistics will be available once budget data is associated with this fiscal year.
            </p>
        </div>

    </div>
</x-app-layout>