<x-app-layout>
    <x-dashboard-header
        title="Statement of Indebtedness"
        subtitle="Record details"
    />

    <div class="max-w-5xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-2">
                <x-secondary-button onclick="window.location.href='{{ route('statements-of-indebtedness.index') }}'">
                    Back to list
                </x-secondary-button>
                <x-secondary-button onclick="window.open('{{ route('statements-of-indebtedness.print', $record) }}', '_blank')">
                    Print
                </x-secondary-button>
            </div>
            <x-primary-button onclick="window.location.href='{{ route('statements-of-indebtedness.edit', $record) }}'">
                Edit
            </x-primary-button>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><dt class="font-semibold text-gray-700">Fiscal Year</dt><dd>{{ $record->fiscalYear->year ?? 'N/A' }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Creditor</dt><dd>{{ $record->creditor }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Date Contracted</dt><dd>{{ optional($record->date_contracted)->format('M d, Y') }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Term/Maturity</dt><dd>{{ $record->term_maturity }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Principal Amount</dt><dd>{{ number_format((float) $record->principal_amount, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Purpose</dt><dd>{{ $record->purpose }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Prev Principal</dt><dd>{{ number_format((float) $record->prev_principal, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Prev Interest</dt><dd>{{ number_format((float) $record->prev_interest, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Prev Total</dt><dd>{{ number_format((float) $record->prev_total, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Due Principal</dt><dd>{{ number_format((float) $record->due_principal, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Due Interest</dt><dd>{{ number_format((float) $record->due_interest, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Due Total</dt><dd>{{ number_format((float) $record->due_total, 2) }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Balance</dt><dd>{{ number_format((float) $record->balance, 2) }}</dd></div>
            </dl>
        </div>
    </div>
</x-app-layout>
