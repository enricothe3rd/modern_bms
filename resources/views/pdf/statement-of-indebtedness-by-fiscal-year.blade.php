<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">STATEMENT OF INDEBTEDNESS</h1>
    <p class="text-sm">Fiscal Year: {{ $fiscalYear->year }}</p>
    <p class="text-sm">Total Records: {{ $records->count() }}</p>
</div>

@php
    $sumPrincipal = 0;
    $sumPrevTotal = 0;
    $sumDueTotal = 0;
    $sumBalance = 0;
@endphp

<div class="mb-4">
    <table class="border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Creditor</th>
                <th class="border p-2 text-left">Date Contracted</th>
                <th class="border p-2 text-left">Term/Maturity</th>
                <th class="border p-2 text-right">Principal</th>
                <th class="border p-2 text-left">Purpose</th>
                <th class="border p-2 text-right">Prev Total</th>
                <th class="border p-2 text-right">Due Total</th>
                <th class="border p-2 text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                @php
                    $sumPrincipal += (float) $record->principal_amount;
                    $sumPrevTotal += (float) $record->prev_total;
                    $sumDueTotal += (float) $record->due_total;
                    $sumBalance += (float) $record->balance;
                @endphp
                <tr>
                    <td class="border p-2">{{ $record->creditor }}</td>
                    <td class="border p-2">{{ optional($record->date_contracted)->format('m/d/Y') }}</td>
                    <td class="border p-2">{{ $record->term_maturity }}</td>
                    <td class="border p-2 text-right">{{ number_format((float) $record->principal_amount, 2) }}</td>
                    <td class="border p-2">{{ $record->purpose }}</td>
                    <td class="border p-2 text-right">{{ number_format((float) $record->prev_total, 2) }}</td>
                    <td class="border p-2 text-right">{{ number_format((float) $record->due_total, 2) }}</td>
                    <td class="border p-2 text-right">{{ number_format((float) $record->balance, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="border p-2 text-center" colspan="8">No records found for this fiscal year.</td>
                </tr>
            @endforelse
            @if($records->count() > 0)
                <tr class="bg-gray-100 font-bold">
                    <td class="border p-2" colspan="3">TOTALS</td>
                    <td class="border p-2 text-right">{{ number_format($sumPrincipal, 2) }}</td>
                    <td class="border p-2"></td>
                    <td class="border p-2 text-right">{{ number_format($sumPrevTotal, 2) }}</td>
                    <td class="border p-2 text-right">{{ number_format($sumDueTotal, 2) }}</td>
                    <td class="border p-2 text-right">{{ number_format($sumBalance, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
</div>
