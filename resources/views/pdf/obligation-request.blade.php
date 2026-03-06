<div class="header text-center mb-4">
    @if($logo_path && file_exists(public_path($logo_path)))
    <div class="logo mb-3">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logo_path))) }}" alt="Logo" style="max-height: 80px; max-width: 200px;">
    </div>
    @endif
    <h1 class="text-2xl font-bold">OBLIGATION REQUEST</h1>
    <p class="text-sm">{{ $organization ?? 'Your Organization Name' }}</p>
    <p class="text-sm">{{ $address ?? 'Organization Address' }}</p>
</div>

<div class="mb-4">
    <table class="border">
        <tr>
            <td class="font-bold bg-gray-100 p-2" style="width: 30%;">OBR Number:</td>
            <td class="p-2">{{ $obr->obr_number ?? 'N/A' }}</td>
            <td class="font-bold bg-gray-100 p-2" style="width: 30%;">Date:</td>
            <td class="p-2">{{ isset($obr->obligation_date) ? $obr->obligation_date->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Department:</td>
            <td class="p-2" colspan="3">{{ $obr->department->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="font-bold bg-gray-100 p-2">Claimant/Payee:</td>
            <td class="p-2" colspan="3">{{ $obr->claimantPayee->name ?? 'N/A' }}</td>
        </tr>
    </table>
</div>

<div class="mb-4">
    <h3 class="font-bold mb-2">PARTICULARS:</h3>
    <div class="border p-3" style="min-height: 100px;">
        {{ $obr->particulars ?? 'N/A' }}
    </div>
</div>

@if(isset($obr->items) && $obr->items->count() > 0)
<div class="mb-4">
    <h3 class="font-bold mb-2">BUDGET BREAKDOWN:</h3>
    <table class="border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Department</th>
                <th class="border p-2 text-left">Expense Type</th>
                <th class="border p-2 text-left">Account</th>
                <th class="border p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($obr->items as $item)
            <tr>
                <td class="border p-2">{{ $item->department->name ?? 'N/A' }}</td>
                <td class="border p-2">{{ $item->expenseType->description ?? 'N/A' }}</td>
                <td class="border p-2">
                    {{ $item->account->description ?? 'N/A' }}
                    @if($item->subAccount)
                        <br><small>{{ $item->subAccount->description }}</small>
                    @endif
                </td>
                <td class="border p-2 text-right currency">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="bg-gray-100 font-bold">
                <td class="border p-2" colspan="3">TOTAL AMOUNT:</td>
                <td class="border p-2 text-right currency">{{ number_format($obr->total_amount ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<div class="signature-section mt-5">
    <div style="display: flex; justify-content: space-between;">
        @if(isset($obr->signatory1) && $obr->signatory1->user)
        <div style="width: 30%; text-align: center;">
            <div class="signature-box"></div>
            <p class="text-sm mt-2">{{ $obr->signatory1->user->name }}</p>
            <p class="text-sm">Signatory 1</p>
            @if($obr->signatory1->signatory_date)
                <p class="text-sm">Date: {{ $obr->signatory1->signatory_date->format('M d, Y') }}</p>
            @endif
        </div>
        @endif

        @if(isset($obr->signatory2) && $obr->signatory2->user)
        <div style="width: 30%; text-align: center;">
            <div class="signature-box"></div>
            <p class="text-sm mt-2">{{ $obr->signatory2->user->name }}</p>
            <p class="text-sm">Signatory 2</p>
            @if($obr->signatory2->signatory_date)
                <p class="text-sm">Date: {{ $obr->signatory2->signatory_date->format('M d, Y') }}</p>
            @endif
        </div>
        @endif

        @if(isset($obr->notedSignatory) && $obr->notedSignatory->user)
        <div style="width: 30%; text-align: center;">
            <div class="signature-box"></div>
            <p class="text-sm mt-2">{{ $obr->notedSignatory->user->name }}</p>
            <p class="text-sm">Noted By</p>
            @if($obr->notedSignatory->signatory_date)
                <p class="text-sm">Date: {{ $obr->notedSignatory->signatory_date->format('M d, Y') }}</p>
            @endif
        </div>
        @endif
    </div>
</div>

<div class="mt-5 text-center text-sm">
    <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    @if(isset($obr->reviewStatus))
        <p>Status: <strong>{{ $obr->reviewStatus->name }}</strong></p>
    @endif
</div>