<x-app-layout>
    <x-dashboard-header title="Plantilla" subtitle="Record details" />
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-2">
                <x-secondary-button onclick="window.location.href='{{ route('plantillas.index') }}'">Back to list</x-secondary-button>
                <x-secondary-button onclick="window.open('{{ route('plantillas.print', $record) }}', '_blank')">Print</x-secondary-button>
            </div>
            <x-primary-button onclick="window.location.href='{{ route('plantillas.edit', $record) }}'">Edit</x-primary-button>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <dl class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div><dt class="font-semibold text-gray-700">Department</dt><dd>{{ $record->department->name ?? 'N/A' }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Group</dt><dd>{{ $record->group_name }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Fiscal Year</dt><dd>{{ $record->fiscalYear->year ?? '-' }}</dd></div>
                <div><dt class="font-semibold text-gray-700">Grand Total</dt><dd>{{ number_format((float) $record->grand_total, 2) }}</dd></div>
            </dl>
            @if($record->notes)
                <div class="mt-4 text-sm"><span class="font-semibold text-gray-700">Notes:</span> {{ $record->notes }}</div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Name</th>
                        <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Position</th>
                        <th class="px-4 py-2 text-right text-xs uppercase text-gray-500">Old</th>
                        <th class="px-4 py-2 text-right text-xs uppercase text-gray-500">New</th>
                        <th class="px-4 py-2 text-left text-xs uppercase text-gray-500">Movements / Tranches</th>
                        <th class="px-4 py-2 text-right text-xs uppercase text-gray-500">Employee Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($record->items as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm">{{ $item->employee_name }}</td>
                            <td class="px-4 py-2 text-sm">{{ $item->position }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ $item->old_count }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ $item->new_count }}</td>
                            <td class="px-4 py-2 text-sm">
                                <div class="space-y-2">
                                    @if($item->groups->isNotEmpty())
                                        @foreach($item->groups->groupBy('group_type') as $type => $groups)
                                            <div class="text-xs font-semibold text-gray-700 uppercase">{{ $type }} Groups</div>
                                            @foreach($groups as $group)
                                                <div class="border border-gray-100 rounded p-2">
                                                    <div class="font-medium">{{ $group->group_label ?: ucfirst($type).' Group' }}</div>
                                                    <div class="text-xs text-gray-600 mb-1">Group Total: {{ number_format((float) $group->group_total, 2) }}</div>
                                                    @foreach($group->movements as $mv)
                                                        <div class="text-xs text-gray-600">
                                                            {{ $mv->label ?: 'Movement' }} | {{ $mv->salarySchedule->title ?? '-' }}
                                                            | SG {{ $mv->salary_grade ?? '-' }} - Step {{ $mv->salary_step ?? '-' }}
                                                            | Amount: {{ $mv->salary_amount !== null ? number_format((float) $mv->salary_amount, 2) : '-' }}
                                                            | Total: {{ number_format((float) $mv->movement_total, 2) }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endforeach
                                    @else
                                        @forelse($item->movements as $mv)
                                            <div class="border border-gray-100 rounded p-2">
                                                <div class="font-medium">{{ $mv->label ?: 'Tranche' }}</div>
                                                <div class="text-xs text-gray-600">{{ $mv->salaryScheduleFrom->title ?? '-' }} -> {{ $mv->salaryScheduleTo->title ?? '-' }}</div>
                                                <div class="text-xs text-gray-600">From: {{ $mv->salary_from_amount !== null ? number_format((float) $mv->salary_from_amount, 2) : '-' }} | To: {{ $mv->salary_to_amount !== null ? number_format((float) $mv->salary_to_amount, 2) : '-' }}</div>
                                                <div class="text-xs font-semibold text-right">Total: {{ number_format((float) $mv->movement_total, 2) }}</div>
                                            </div>
                                        @empty
                                            <div class="text-gray-500 text-xs">No movements</div>
                                        @endforelse
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-right">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-right font-semibold">Grand Total</td>
                        <td class="px-4 py-2 text-right font-semibold">{{ number_format((float) $record->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
