<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">FINANCIAL REPORT</h1>
    <p class="text-sm">{{ $organization ?? 'Your Organization Name' }}</p>
    <p class="text-sm">Period: {{ $period ?? 'N/A' }}</p>
</div>

<div class="mb-4">
    <h3 class="font-bold mb-2">SUMMARY:</h3>
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2">Total Budget:</td>
            <td class="p-2 text-right">₱{{ number_format($summary['total_budget'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Total Obligated:</td>
            <td class="p-2 text-right">₱{{ number_format($summary['total_obligated'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Remaining Balance:</td>
            <td class="p-2 text-right">₱{{ number_format($summary['remaining_balance'] ?? 0, 2) }}</td>
        </tr>
    </table>
</div>

@if(isset($departments) && count($departments) > 0)
<div class="mb-4">
    <h3 class="font-bold mb-2">DEPARTMENT BREAKDOWN:</h3>
    <table class="border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Department</th>
                <th class="border p-2 text-right">Budget</th>
                <th class="border p-2 text-right">Obligated</th>
                <th class="border p-2 text-right">Balance</th>
                <th class="border p-2 text-right">Utilization %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $dept)
            <tr>
                <td class="border p-2">{{ $dept['name'] }}</td>
                <td class="border p-2 text-right">₱{{ number_format($dept['budget'], 2) }}</td>
                <td class="border p-2 text-right">₱{{ number_format($dept['obligated'], 2) }}</td>
                <td class="border p-2 text-right">₱{{ number_format($dept['balance'], 2) }}</td>
                <td class="border p-2 text-right">{{ number_format($dept['utilization'], 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    <p class="text-xs">This is a system-generated report</p>
</div>