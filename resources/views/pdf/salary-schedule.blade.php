<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">SALARY SCHEDULE</h1>
    <p class="text-sm">{{ $record->title ?: 'Salary Schedule #' . $record->id }}</p>
    <p class="text-sm">Effective {{ optional($record->effective_date)->format('F d, Y') }} | Fiscal Year {{ $record->fiscalYear->year ?? 'N/A' }}</p>
</div>

<table class="border">
    <thead>
        <tr class="bg-gray-100">
            <th class="border p-2 text-left">Salary Grade</th>
            @for($step = 1; $step <= $record->total_steps; $step++)
                <th class="border p-2 text-right">Step {{ $step }}</th>
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

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
</div>
