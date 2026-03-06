<x-app-layout>
    <x-dashboard-header title="Plantilla" subtitle="Department plantilla groups with salary movement totals." />
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div></div>
            <x-primary-button onclick="window.location.href='{{ route('plantillas.create') }}'">Create Plantilla</x-primary-button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 mb-6 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input-label for="department_id" value="Department" />
                    <select name="department_id" id="department_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">
                        <option value="">All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ (string) request('department_id') === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <x-input-label for="search" value="Group Name" />
                    <x-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ request('search') }}" />
                </div>
                <div class="flex items-end gap-2">
                    <x-primary-button type="submit">Filter</x-primary-button>
                    <x-secondary-button onclick="window.location.href='{{ route('plantillas.index') }}'">Clear</x-secondary-button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">FY</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employees</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Grand Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $record)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $record->department->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $record->group_name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $record->fiscalYear->year ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $record->items->count() }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ number_format((float) $record->grand_total, 2) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('plantillas.show', $record) }}" class="text-blue-600 hover:text-blue-700">View</a>
                                    <a href="{{ route('plantillas.edit', $record) }}" class="text-yellow-600 hover:text-yellow-700">Edit</a>
                                    <form method="POST" action="{{ route('plantillas.destroy', $record) }}" onsubmit="return confirm('Delete this plantilla?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No plantilla records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $records->links() }}</div>
    </div>
</x-app-layout>
