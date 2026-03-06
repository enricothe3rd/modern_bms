<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">STATEMENT OF INDEBTEDNESS</h1>
    <p class="text-sm">Fiscal Year: {{ $record->fiscalYear->year ?? 'N/A' }}</p>
    <p class="text-sm">Creditor: {{ $record->creditor }}</p>
</div>

<div class="mb-4">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 25%;">Fiscal Year</td>
            <td class="p-2">{{ $record->fiscalYear->year ?? 'N/A' }}</td>
            <td class="font-bold bg-gray-100 p-2" style="width: 25%;">Date Contracted</td>
            <td class="p-2">{{ optional($record->date_contracted)->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Creditor</td>
            <td class="p-2">{{ $record->creditor }}</td>
            <td class="font-bold bg-gray-100 p-2">Term/Maturity</td>
            <td class="p-2">{{ $record->term_maturity }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Principal Amount</td>
            <td class="p-2 text-right">{{ number_format((float) $record->principal_amount, 2) }}</td>
            <td class="font-bold bg-gray-100 p-2">Balance</td>
            <td class="p-2 text-right">{{ number_format((float) $record->balance, 2) }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Purpose</td>
            <td class="p-2" colspan="3">{{ $record->purpose }}</td>
        </tr>
    </table>
</div>

<div class="mb-4">
    <h3 class="font-bold mb-2">PREVIOUS AMOUNTS:</h3>
    <table class="border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Field</th>
                <th class="border p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border p-2">Prev Principal</td>
                <td class="border p-2 text-right">{{ number_format((float) $record->prev_principal, 2) }}</td>
            </tr>
            <tr>
                <td class="border p-2">Prev Interest</td>
                <td class="border p-2 text-right">{{ number_format((float) $record->prev_interest, 2) }}</td>
            </tr>
            <tr class="bg-gray-100 font-bold">
                <td class="border p-2">Prev Total</td>
                <td class="border p-2 text-right">{{ number_format((float) $record->prev_total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mb-4">
    <h3 class="font-bold mb-2">DUE AMOUNTS:</h3>
    <table class="border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Field</th>
                <th class="border p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border p-2">Due Principal</td>
                <td class="border p-2 text-right">{{ number_format((float) $record->due_principal, 2) }}</td>
            </tr>
            <tr>
                <td class="border p-2">Due Interest</td>
                <td class="border p-2 text-right">{{ number_format((float) $record->due_interest, 2) }}</td>
            </tr>
            <tr class="bg-gray-100 font-bold">
                <td class="border p-2">Due Total</td>
                <td class="border p-2 text-right">{{ number_format((float) $record->due_total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
</div>
