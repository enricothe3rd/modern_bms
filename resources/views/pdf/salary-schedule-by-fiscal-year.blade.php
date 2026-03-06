<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">SALARY SCHEDULES</h1>
    <p class="text-sm">Fiscal Year: {{ $fiscalYear->year }}</p>
    <p class="text-sm">Total Schedules: {{ $records->count() }}</p>
</div>

@forelse($records as $record)
    @php
        $grid = [];
        foreach ($record->cells as $cell) {
            $grid[$cell->salary_grade][$cell->step_no] = $cell->amount;
        }
    @endphp
    <div class="mb-4 no-break">
        <table class="border">
            <tr>
                <td class="font-bold bg-gray-100 p-2" style="width: 18%;">Title</td>
                <td class="p-2">{{ $record->title ?: 'Salary Schedule #' . $record->id }}</td>
                <td class="font-bold bg-gray-100 p-2" style="width: 18%;">Effective Date</td>
                <td class="p-2">{{ optional($record->effective_date)->format('M d, Y') }}</td>
            </tr>
        </table>
        <table class="border mt-2">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-left">SG</th>
                    @for($step = 1; $step <= $record->total_steps; $step++)
                        <th class="border p-2 text-right">S{{ $step }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @for($grade = 1; $grade <= $record->max_grade; $grade++)
                    <tr>
                        <td class="border p-2">{{ $grade }}</td>
                        @for($step = 1; $step <= $record->total_steps; $step++)
                            <td class="border p-2 text-right">{{ isset($grid[$grade][$step]) && $grid[$grade][$step] !== null ? number_format((float) $grid[$grade][$step], 2) : '-' }}</td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
@empty
    <div class="text-center text-sm">No salary schedules found for this fiscal year.</div>
@endforelse
