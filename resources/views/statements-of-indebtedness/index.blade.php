<x-app-layout>
    <x-dashboard-header
        title="Statements of Indebtedness"
        subtitle="Manage statement of indebtedness records by fiscal year."
    />

    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div></div>
            <x-primary-button onclick="window.location.href='{{ route('statements-of-indebtedness.create') }}'">
                Create Record
            </x-primary-button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4" id="soi-filter-form">
                <div>
                    <x-input-label for="fiscal_year_id" value="Fiscal Year" />
                    <select name="fiscal_year_id" id="fiscal_year_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">
                        <option value="">All Fiscal Years</option>
                        @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}" {{ (string) request('fiscal_year_id') === (string) $fy->id ? 'selected' : '' }}>
                                {{ $fy->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="search" value="Creditor" />
                    <x-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ request('search') }}" placeholder="Search creditor" />
                </div>
                <div class="flex items-end gap-2">
                    <x-primary-button type="submit">Filter</x-primary-button>
                    <x-secondary-button onclick="window.location.href='{{ route('statements-of-indebtedness.index') }}'">Clear</x-secondary-button>
                    <button type="button"
                        id="printFiscalYearBtn"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm font-semibold">
                        Print Fiscal Year
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiscal Year</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creditor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Contracted</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Principal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $record->fiscalYear->year ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $record->creditor }}</td>
                            <td class="px-4 py-3 text-sm">{{ optional($record->date_contracted)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm">{{ number_format((float) $record->principal_amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm">{{ number_format((float) $record->due_total, 2) }}</td>
                            <td class="px-4 py-3 text-sm">{{ number_format((float) $record->balance, 2) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('statements-of-indebtedness.show', $record) }}" class="text-blue-600 hover:text-blue-700">View</a>
                                    <a href="{{ route('statements-of-indebtedness.edit', $record) }}" class="text-yellow-600 hover:text-yellow-700">Edit</a>
                                    <form action="{{ route('statements-of-indebtedness.destroy', $record) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $records->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const printBtn = document.getElementById('printFiscalYearBtn');
            const fiscalYearSelect = document.getElementById('fiscal_year_id');

            if (!printBtn || !fiscalYearSelect) {
                return;
            }

            printBtn.addEventListener('click', function () {
                const fiscalYearId = fiscalYearSelect.value;

                if (!fiscalYearId) {
                    alert('Select a fiscal year first to print.');
                    return;
                }

                const url = new URL('{{ route('statements-of-indebtedness.print-fiscal-year') }}', window.location.origin);
                url.searchParams.set('fiscal_year_id', fiscalYearId);
                window.open(url.toString(), '_blank');
            });
        });
    </script>
</x-app-layout>
