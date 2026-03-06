<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">STATEMENT OF STATUTORY OBLIGATIONS</h1>
    <p class="text-sm">Fiscal Year: {{ $record->fiscalYear->year ?? 'N/A' }}</p>
    <p class="text-sm">{{ $record->title ?: 'Statement #' . $record->id }}</p>
</div>

@if($record->remarks)
<div class="mb-4">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 20%;">Remarks</td>
            <td class="p-2">{{ $record->remarks }}</td>
        </tr>
    </table>
</div>
@endif

@foreach($record->categories as $category)
<div class="mb-4 no-break">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 30%;">Category</td>
            <td class="p-2">{{ $category->category_number }} - {{ $category->category_name }}</td>
        </tr>
    </table>
    <table class="border mt-2">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Code</th>
                <th class="border p-2 text-left">Description</th>
                <th class="border p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($category->items as $item)
            <tr>
                <td class="border p-2">{{ $item->code ?: '-' }}</td>
                <td class="border p-2">{{ $item->description }}</td>
                <td class="border p-2 text-right">{{ $item->amount !== null ? number_format((float) $item->amount, 2) : '-' }}</td>
            </tr>
            @empty
            <tr><td class="border p-2 text-center" colspan="3">No items for this category.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
</div>
