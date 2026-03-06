<x-app-layout>
    @section('title', 'Edit Fiscal Year')

    <x-dashboard-header
        title="Edit Fiscal Year {{ $fiscalYear->year }}"
        subtitle="Update fiscal year information"
    />

    <div class="max-w-4xl mx-auto p-6">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('fiscal-years.index') }}" class="text-blue-600 hover:text-blue-700">
                ← Back to Fiscal Years
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden ring-1 ring-gray-900/5 p-6">
            <form action="{{ route('fiscal-years.update', $fiscalYear) }}" method="POST">
                @csrf
                @method('PUT')

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
                    <!-- Year -->
                    <div>
                        <x-input-label value="Fiscal Year *" />
                        <x-input type="number" name="year" value="{{ old('year', $fiscalYear->year) }}" 
                                 min="2020" max="2050" class="mt-1 block w-full" required />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label value="Description" />
                        <x-input type="text" name="description" value="{{ old('description', $fiscalYear->description) }}" 
                                 placeholder="e.g., Fiscal Year 2025" class="mt-1 block w-full" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <x-input-label value="Start Date *" />
                        <x-input type="date" name="start_date" value="{{ old('start_date', $fiscalYear->start_date->format('Y-m-d')) }}" 
                                 class="mt-1 block w-full" required />
                    </div>

                    <!-- End Date -->
                    <div>
                        <x-input-label value="End Date *" />
                        <x-input type="date" name="end_date" value="{{ old('end_date', $fiscalYear->end_date->format('Y-m-d')) }}" 
                                 class="mt-1 block w-full" required />
                    </div>
                </div>

                <!-- Status Options -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', $fiscalYear->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Active (can be used for budget planning)
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_current" id="is_current" value="1" 
                               {{ old('is_current', $fiscalYear->is_current) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_current" class="ml-2 text-sm text-gray-700">
                            Set as current fiscal year (will unset other current years)
                        </label>
                    </div>
                </div>

                <!-- Current Status Info -->
                @if($fiscalYear->is_current)
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Current Fiscal Year</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    This is currently set as the active fiscal year for the system.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Buttons -->
                <div class="flex justify-end space-x-2 mt-8">
                    <a href="{{ route('fiscal-years.index') }}" 
                       class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">
                        Update Fiscal Year
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>