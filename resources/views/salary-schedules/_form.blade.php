@php
    $record = $record ?? null;
    $currentFiscalYear = $currentFiscalYear ?? null;
    $grid = $grid ?? [];
    $maxGrade = old('max_grade', $record->max_grade ?? 33);
    $totalSteps = old('total_steps', $record->total_steps ?? 8);
@endphp

@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
        <div class="font-semibold mb-2">Please fix the following errors:</div>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Schedule Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <x-input-label for="fiscal_year_id" value="Fiscal Year *" />
            <select name="fiscal_year_id" id="fiscal_year_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1" required>
                <option value="">Select Fiscal Year</option>
                @foreach($fiscalYears as $fiscalYear)
                    <option value="{{ $fiscalYear->id }}" {{ (string) old('fiscal_year_id', $record->fiscal_year_id ?? $currentFiscalYear?->id) === (string) $fiscalYear->id ? 'selected' : '' }}>
                        {{ $fiscalYear->year }}{{ $fiscalYear->is_current ? ' (Current)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="effective_date" value="Effective Date *" />
            <x-input id="effective_date" name="effective_date" type="date" class="mt-1 block w-full" value="{{ old('effective_date', isset($record) && $record->effective_date ? $record->effective_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required />
        </div>
        <div>
            <x-input-label for="title" value="Title" />
            <x-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $record->title ?? '') }}" />
        </div>
        <div>
            <x-input-label for="max_grade" value="Max Grade *" />
            <x-input id="max_grade" name="max_grade" type="number" min="1" max="99" class="mt-1 block w-full" value="{{ $maxGrade }}" required />
        </div>
        <div>
            <x-input-label for="total_steps" value="Total Steps *" />
            <x-input id="total_steps" name="total_steps" type="number" min="1" max="12" class="mt-1 block w-full" value="{{ $totalSteps }}" required />
        </div>
    </div>
    <div class="mt-6">
        <x-input-label for="notes" value="Notes" />
        <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">{{ old('notes', $record->notes ?? '') }}</textarea>
    </div>
    <p class="text-xs text-gray-500 mt-3">Changing max grade or total steps requires saving the form to rebuild the grid dimensions.</p>
</div>

<div class="mb-6">
    <h2 class="text-xl font-semibold mb-3">Salary Matrix</h2>
    @include('salary-schedules._grid-table', ['grid' => $grid, 'maxGrade' => (int) $maxGrade, 'totalSteps' => (int) $totalSteps, 'readonly' => false])
</div>

<div class="flex justify-end gap-2">
    <x-secondary-button onclick="window.location.href='{{ route('salary-schedules.index') }}'">Cancel</x-secondary-button>
    <x-primary-button type="submit">{{ $record ? 'Update Schedule' : 'Create Schedule' }}</x-primary-button>
</div>
