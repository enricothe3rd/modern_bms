@php
    $readonly = $readonly ?? false;
    $maxGrade = $maxGrade ?? ($record->max_grade ?? 33);
    $totalSteps = $totalSteps ?? ($record->total_steps ?? 8);
@endphp

<div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-3 py-2 text-left">Salary Grade</th>
                @for($step = 1; $step <= $totalSteps; $step++)
                    <th class="border border-gray-300 px-3 py-2 text-center">Step {{ $step }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @for($grade = 1; $grade <= $maxGrade; $grade++)
                <tr>
                    <td class="border border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ $grade }}</td>
                    @for($step = 1; $step <= $totalSteps; $step++)
                        @php
                            $value = old("cells.$grade.$step", $grid[$grade][$step] ?? null);
                        @endphp
                        <td class="border border-gray-300 px-1 py-1">
                            @if($readonly)
                                <div class="text-right px-2 py-1">{{ $value !== null && $value !== '' ? number_format((float) $value, 2) : '-' }}</div>
                            @else
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="cells[{{ $grade }}][{{ $step }}]"
                                    value="{{ $value }}"
                                    class="w-full border border-gray-200 rounded px-2 py-1 text-right"
                                />
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
</div>
