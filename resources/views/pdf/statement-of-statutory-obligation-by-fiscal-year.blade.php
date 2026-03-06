<div class="header text-center mb-4">
    <h1 class="text-2xl font-bold">STATEMENT OF STATUTORY OBLIGATIONS</h1>
    <p class="text-sm">Fiscal Year: {{ $fiscalYear->year }}</p>
    <p class="text-sm">Total Statements: {{ $records->count() }}</p>
</div>

@forelse($records as $record)
<div class="mb-4 no-break">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 18%;">Statement</td>
            <td class="p-2">{{ $record->title ?: 'Statement #' . $record->id }}</td>
            <td class="font-bold bg-gray-100 p-2" style="width: 18%;">Categories</td>
            <td class="p-2">{{ $record->categories->count() }}</td>
        </tr>
    </table>
    <table class="border mt-2">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Category</th>
                <th class="border p-2 text-left">Code</th>
                <th class="border p-2 text-left">Description</th>
                <th class="border p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $hasRows = false;
            @endphp
            @foreach($record->categories as $category)
                @if($category->items->count())
                    @foreach($category->items as $item)
                        @php
                            $hasRows = true;
                        @endphp
                        <tr>
                            <td class="border p-2">{{ $category->category_number }} - {{ $category->category_name }}</td>
                            <td class="border p-2">{{ $item->code ?: '-' }}</td>
                            <td class="border p-2">{{ $item->description }}</td>
                            <td class="border p-2 text-right">{{ $item->amount !== null ? number_format((float) $item->amount, 2) : '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    @php
                        $hasRows = true;
                    @endphp
                    <tr>
                        <td class="border p-2">{{ $category->category_number }} - {{ $category->category_name }}</td>
                        <td class="border p-2" colspan="3">No items</td>
                    </tr>
                @endif
            @endforeach
            @if(!$hasRows)
                <tr><td class="border p-2 text-center" colspan="4">No categories found.</td></tr>
            @endif
        </tbody>
    </table>
</div>
@empty
<div class="text-center text-sm">No statements found for this fiscal year.</div>
@endforelse

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
</div>
