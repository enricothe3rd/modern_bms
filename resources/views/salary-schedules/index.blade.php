<x-app-layout>
    <x-dashboard-header title="Salary Schedules" subtitle="Manage salary grade x step schedules." />

    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div></div>
            <x-primary-button onclick="window.location.href='{{ route('salary-schedules.create') }}'">Create Schedule</x-primary-button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="fiscal_year_id" value="Fiscal Year" />
                    <select name="fiscal_year_id" id="fiscal_year_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">
                        <option value="">All Fiscal Years</option>
                        @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}" {{ (string) request('fiscal_year_id') === (string) $fy->id ? 'selected' : '' }}>{{ $fy->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="search" value="Title" />
                    <x-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ request('search') }}" />
                </div>
                <div class="flex items-end gap-2">
                    <x-primary-button type="submit">Filter</x-primary-button>
                    <x-secondary-button onclick="window.location.href='{{ route('salary-schedules.index') }}'">Clear</x-secondary-button>
                    <button type="button" id="printFiscalYearBtn" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm font-semibold">Print Fiscal Year</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiscal Year</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effective Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grid</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $record)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $record->fiscalYear->year ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $record->title ?: 'Salary Schedule #' . $record->id }}</td>
                            <td class="px-4 py-3 text-sm">{{ optional($record->effective_date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm">SG 1-{{ $record->max_grade }}, Steps 1-{{ $record->total_steps }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('salary-schedules.show', $record) }}" class="text-blue-600 hover:text-blue-700">View</a>
                                    <a href="{{ route('salary-schedules.edit', $record) }}" class="text-yellow-600 hover:text-yellow-700">Edit</a>
                                    <form method="POST" action="{{ route('salary-schedules.destroy', $record) }}" onsubmit="return confirm('Delete this schedule?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No salary schedules found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $records->links() }}</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('printFiscalYearBtn');
            const fy = document.getElementById('fiscal_year_id');
            if (!btn || !fy) return;
            btn.addEventListener('click', function () {
                if (!fy.value) return alert('Select a fiscal year first to print.');
                const url = new URL('{{ route('salary-schedules.print-fiscal-year') }}', window.location.origin);
                url.searchParams.set('fiscal_year_id', fy.value);
                window.open(url.toString(), '_blank');
            });
        });
    </script>
</x-app-layout>
