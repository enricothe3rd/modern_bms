<x-app-layout>
    <x-dashboard-header title="Statement of Statutory Obligations" subtitle="Record details" />
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-2">
                <x-secondary-button onclick="window.location.href='{{ route('statements-of-statutory-obligations.index') }}'">Back to list</x-secondary-button>
                <x-secondary-button onclick="window.open('{{ route('statements-of-statutory-obligations.print', $record) }}', '_blank')">Print</x-secondary-button>
            </div>
            <x-primary-button onclick="window.location.href='{{ route('statements-of-statutory-obligations.edit', $record) }}'">Edit</x-primary-button>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div><dt class="font-semibold text-gray-700">Fiscal Year</dt><dd>{{ $record->fiscalYear->year ?? 'N/A' }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Title</dt><dd>{{ $record->title ?: 'Statement #' . $record->id }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Remarks</dt><dd>{{ $record->remarks ?: '-' }}</dd></div>
            </dl>
        </div>

        @forelse($record->categories as $category)
            <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                <h3 class="text-lg font-semibold mb-3">{{ $category->category_number }} - {{ $category->category_name }}</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Code</th>
                            <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Description</th>
                            <th class="px-4 py-2 text-right text-xs uppercase text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($category->items as $item)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $item->code ?: '-' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->description }}</td>
                                <td class="px-4 py-2 text-sm text-right">{{ $item->amount !== null ? number_format((float) $item->amount, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-2 text-center text-sm text-gray-500">No items</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-gray-500">No categories found.</div>
        @endforelse
    </div>
</x-app-layout>
