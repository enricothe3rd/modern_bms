<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">STATEMENT OF FUNDING SOURCES</h1>
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

@forelse($record->categories as $category)
<div class="mb-4 no-break">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 20%;">Category</td>
            <td class="p-2">{{ $category->category_number }} - {{ $category->category_name }}</td>
            <td class="font-bold bg-gray-100 p-2" style="width: 20%;">Amount</td>
            <td class="p-2 text-right">{{ $category->amount !== null ? number_format((float) $category->amount, 2) : '-' }}</td>
        </tr>
    </table>

    <table class="border mt-2">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Particulars</th>
                <th class="border p-2 text-left">Account Classification</th>
                <th class="border p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($category->items as $item)
            <tr>
                <td class="border p-2">{{ $item->particulars }}</td>
                <td class="border p-2">{{ $item->account_classification ?: '-' }}</td>
                <td class="border p-2 text-right">{{ $item->amount !== null ? number_format((float) $item->amount, 2) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td class="border p-2 text-center" colspan="3">No items for this category.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@empty
<div class="mb-4 text-center text-sm">No categories found.</div>
@endforelse

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
</div>
