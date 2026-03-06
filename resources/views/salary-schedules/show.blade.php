<x-app-layout>
    <x-dashboard-header title="Salary Schedule" subtitle="Schedule details" />
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-2">
                <x-secondary-button onclick="window.location.href='{{ route('salary-schedules.index') }}'">Back to list</x-secondary-button>
                <x-secondary-button onclick="window.open('{{ route('salary-schedules.print', $record) }}', '_blank')">Print</x-secondary-button>
            </div>
            <x-primary-button onclick="window.location.href='{{ route('salary-schedules.edit', $record) }}'">Edit</x-primary-button>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <dl class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div><dt class="font-semibold text-gray-700">Fiscal Year</dt><dd>{{ $record->fiscalYear->year ?? 'N/A' }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Title</dt><dd>{{ $record->title ?: 'Salary Schedule #' . $record->id }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Effective Date</dt><dd>{{ optional($record->effective_date)->format('F d, Y') }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Grid</dt><dd>SG 1-{{ $record->max_grade }}, Steps 1-{{ $record->total_steps }}</dd></div>
            </dl>
        </div>

        @include('salary-schedules._grid-table', ['grid' => $grid, 'maxGrade' => $record->max_grade, 'totalSteps' => $record->total_steps, 'readonly' => true])
    </div>
</x-app-layout>
