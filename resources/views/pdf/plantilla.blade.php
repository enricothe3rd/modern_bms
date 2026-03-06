<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">PLANTILLA</h1>
    <p class="text-sm">{{ $record->group_name }}</p>
    <p class="text-sm">{{ $record->department->name ?? 'N/A' }} | Fiscal Year {{ $record->fiscalYear->year ?? '-' }}</p>
</div>

<table class="border">
    <thead>
        <tr class="bg-gray-100">
            <th class="border p-2 text-left">Name</th>
            <th class="border p-2 text-left">Position</th>
            <th class="border p-2 text-right">Old</th>
            <th class="border p-2 text-right">New</th>
            <th class="border p-2 text-left">Movements / Tranches</th>
            <th class="border p-2 text-right">Employee Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($record->items as $item)
            <tr>
                <td class="border p-2">{{ $item->employee_name }}</td>
                <td class="border p-2">{{ $item->position }}</td>
                <td class="border p-2 text-right">{{ $item->old_count }}</td>
                <td class="border p-2 text-right">{{ $item->new_count }}</td>
                <td class="border p-2">
                    @if($item->groups->isNotEmpty())
                        @foreach($item->groups->groupBy('group_type') as $type => $groups)
                            <div><strong>{{ strtoupper($type) }} GROUPS</strong></div>
                            @foreach($groups as $group)
                                <div style="margin-bottom:4px;">
                                    <strong>{{ $group->group_label ?: ucfirst($type).' Group' }}</strong>
                                    <small>(Total {{ number_format((float) $group->group_total, 2) }})</small><br>
                                    @foreach($group->movements as $mv)
                                        <small>{{ $mv->label ?: 'Movement' }} | {{ $mv->salarySchedule->title ?? '-' }} | SG {{ $mv->salary_grade ?? '-' }}-{{ $mv->salary_step ?? '-' }} | Amt {{ $mv->salary_amount !== null ? number_format((float) $mv->salary_amount, 2) : '-' }} | Total {{ number_format((float) $mv->movement_total, 2) }}</small><br>
                                    @endforeach
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        @forelse($item->movements as $mv)
                            <div style="margin-bottom:4px;">
                                <strong>{{ $mv->label ?: 'Tranche' }}</strong><br>
                                <small>{{ $mv->salaryScheduleFrom->title ?? '-' }} -> {{ $mv->salaryScheduleTo->title ?? '-' }}</small><br>
                                <small>From {{ $mv->salary_from_amount !== null ? number_format((float) $mv->salary_from_amount, 2) : '-' }} | To {{ $mv->salary_to_amount !== null ? number_format((float) $mv->salary_to_amount, 2) : '-' }} | Total {{ number_format((float) $mv->movement_total, 2) }}</small>
                            </div>
                        @empty
                            -
                        @endforelse
                    @endif
                </td>
                <td class="border p-2 text-right">{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @endforeach
        <tr class="bg-gray-100 font-bold">
            <td class="border p-2 text-right" colspan="6">Grand Total</td>
            <td class="border p-2 text-right">{{ number_format((float) $record->grand_total, 2) }}</td>
        </tr>
    </tbody>
</table>
