<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">PLANTILLA (Fiscal Year)</h1>
    <p class="text-sm">Fiscal Year {{ $fiscalYear->year }}</p>
</div>

@forelse($records as $record)
<div class="mb-4 no-break">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 15%;">Department</td>
            <td class="p-2">{{ $record->department->name ?? 'N/A' }}</td>
            <td class="font-bold bg-gray-100 p-2" style="width: 15%;">Group</td>
            <td class="p-2">{{ $record->group_name }}</td>
            <td class="font-bold bg-gray-100 p-2" style="width: 15%;">Grand Total</td>
            <td class="p-2 text-right">{{ number_format((float) $record->grand_total, 2) }}</td>
        </tr>
    </table>
    <table class="border mt-2">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Name</th>
                <th class="border p-2 text-left">Position</th>
                <th class="border p-2 text-right">Old</th>
                <th class="border p-2 text-right">New</th>
                <th class="border p-2 text-left">Details</th>
                <th class="border p-2 text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $item)
                @php
                    $toTotal = 0;
                    $fromTotal = 0;
                    if ($item->groups->isNotEmpty()) {
                        foreach ($item->groups as $g) {
                            if ($g->group_type === 'to') {
                                $toTotal += (float) $g->group_total;
                            } elseif ($g->group_type === 'from') {
                                $fromTotal += (float) $g->group_total;
                            }
                        }
                    }
                    $rawDiff = $toTotal - $fromTotal;
                    $indicator = $rawDiff > 0 ? 'Increase' : ($rawDiff < 0 ? 'Decrease' : 'No Change');
                @endphp
                <tr>
                    <td class="border p-2">{{ $item->employee_name }}</td>
                    <td class="border p-2">{{ $item->position }}</td>
                    <td class="border p-2 text-right">{{ $item->old_count }}</td>
                    <td class="border p-2 text-right">{{ $item->new_count }}</td>
                    <td class="border p-2">
                        @if($item->groups->isNotEmpty())
                            <div><strong>Status:</strong> {{ $indicator }}</div>
                            @foreach($item->groups->groupBy('group_type') as $type => $groups)
                                <div style="margin-top:4px;">
                                    <strong>{{ strtoupper($type) }} GROUPS</strong>
                                    @foreach($groups as $group)
                                        <div style="margin-left:8px;">
                                            <small>{{ $group->group_label ?: ucfirst($type).' Group' }} ({{ number_format((float) $group->group_total, 2) }})</small><br>
                                            @foreach($group->movements as $mv)
                                                <small style="margin-left:8px;">- {{ $mv->label ?: 'Movement' }} | {{ $mv->salarySchedule->title ?? '-' }} | SG {{ $mv->salary_grade ?? '-' }}-{{ $mv->salary_step ?? '-' }} | {{ $mv->salary_amount !== null ? number_format((float) $mv->salary_amount, 2) : '-' }}</small><br>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <small>No grouped details</small>
                        @endif
                    </td>
                    <td class="border p-2 text-right">{{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@empty
<div class="text-center text-sm">No plantilla records found for this fiscal year.</div>
@endforelse
