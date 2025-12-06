<x-app-layout>
    @section('title', 'View Obligation Request')

    <x-dashboard-header
        title="Obligation Request Details"
        subtitle="View OBR information"
    />

    <div class="max-w-7xl mx-auto p-6">

        <div class="mb-6">
            <a href="{{ route('obligation-requests.index') }}" class="text-blue-600 hover:text-blue-700">
                ← Back to Obligation Requests
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8">
            <!-- Header -->
            <div class="border-b pb-4 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $obr->obr_number }}</h2>
                        <p class="text-gray-600 mt-1">{{ $obr->obligation_date->format('F d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($obr->status === 'approved') bg-green-100 text-green-800
                        @elseif($obr->status === 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($obr->status) }}
                    </span>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Responsibility Center</h3>
                    <p class="text-gray-900">{{ $obr->department->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Claimant Payee</h3>
                    <p class="text-gray-900">{{ $obr->claimantPayee->name }}</p>
                </div>
            </div>

            <!-- Particulars -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Particulars</h3>
                <p class="text-gray-900">{{ $obr->particulars }}</p>
            </div>

            <!-- Optional Fields -->
            @if($obr->optional_field_1 || $obr->optional_field_2)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($obr->optional_field_1)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Optional Field 1</h3>
                    <p class="text-gray-900">{{ $obr->optional_field_1 }}</p>
                </div>
                @endif
                @if($obr->optional_field_2)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Optional Field 2</h3>
                    <p class="text-gray-900">{{ $obr->optional_field_2 }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Signatories -->
            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Signatories</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($obr->signatory1)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Signatory 1</p>
                        <p class="font-semibold">{{ $obr->signatory1->user->name }}</p>
                        @if($obr->signatory1->signatory_date)
                        <p class="text-sm text-gray-600 mt-1">{{ $obr->signatory1->signatory_date->format('M d, Y') }}</p>
                        @endif
                    </div>
                    @endif

                    @if($obr->signatory2)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Signatory 2</p>
                        <p class="font-semibold">{{ $obr->signatory2->user->name }}</p>
                        @if($obr->signatory2->signatory_date)
                        <p class="text-sm text-gray-600 mt-1">{{ $obr->signatory2->signatory_date->format('M d, Y') }}</p>
                        @endif
                    </div>
                    @endif

                    @if($obr->notedSignatory)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Noted By</p>
                        <p class="font-semibold">{{ $obr->notedSignatory->user->name }}</p>
                        @if($obr->notedSignatory->signatory_date)
                        <p class="text-sm text-gray-600 mt-1">{{ $obr->notedSignatory->signatory_date->format('M d, Y') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Line Items -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Line Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fund Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expense Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Account</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($obr->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($item->fundType)
                                        {{ $item->fundType->description }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->department->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->expenseType->description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($item->account)
                                        {{ $item->account->code }} - {{ $item->account->description }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($item->subAccount)
                                        {{ $item->subAccount->code }} - {{ $item->subAccount->description }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">₱{{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="6" class="px-4 py-3 text-sm text-gray-900 text-right">Total Amount:</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">₱{{ number_format($obr->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
