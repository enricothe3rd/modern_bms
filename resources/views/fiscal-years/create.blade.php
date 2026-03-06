<x-app-layout>
    @section('title', 'Create Fiscal Year')

    <x-dashboard-header
        title="Create Fiscal Year"
        subtitle="Add a new fiscal year to the system"
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
            <form action="{{ route('fiscal-years.store') }}" method="POST">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Year -->
                    <div>
                        <x-input-label value="Fiscal Year *" />
                        <select name="year" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" required>
                            <option value="">Select Year</option>
                            @foreach($suggestedYears as $year)
                                <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                            @for($i = 2020; $i <= 2050; $i++)
                                @if(!in_array($i, $suggestedYears))
                                    <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endif
                            @endfor
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label value="Description" />
                        <x-input type="text" name="description" value="{{ old('description') }}" 
                                 placeholder="e.g., Fiscal Year 2025" class="mt-1 block w-full" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <x-input-label value="Start Date *" />
                        <x-input type="date" name="start_date" value="{{ old('start_date') }}" 
                                 class="mt-1 block w-full" required />
                    </div>

                    <!-- End Date -->
                    <div>
                        <x-input-label value="End Date *" />
                        <x-input type="date" name="end_date" value="{{ old('end_date') }}" 
                                 class="mt-1 block w-full" required />
                    </div>
                </div>

                <!-- Status Options -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Active (can be used for budget planning)
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_current" id="is_current" value="1" 
                               {{ old('is_current') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_current" class="ml-2 text-sm text-gray-700">
                            Set as current fiscal year (will unset other current years)
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-2 mt-8">
                    <a href="{{ route('fiscal-years.index') }}" 
                       class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition">
                        Create Fiscal Year
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        // Auto-populate dates when year is selected
        document.querySelector('select[name="year"]').addEventListener('change', function() {
            const year = this.value;
            if (year) {
                document.querySelector('input[name="start_date"]').value = `${year}-01-01`;
                document.querySelector('input[name="end_date"]').value = `${year}-12-31`;
                
                // Auto-populate description if empty
                const descInput = document.querySelector('input[name="description"]');
                if (!descInput.value) {
                    descInput.value = `Fiscal Year ${year}`;
                }
            }
        });
    </script>
</x-app-layout>