@php
    $isEdit = isset($record);
@endphp

@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
        <div class="font-semibold mb-2">Please fix the following errors:</div>
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="fiscal_year_id" value="Fiscal Year *" />
            <select name="fiscal_year_id" id="fiscal_year_id" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" required>
                <option value="">Select Fiscal Year</option>
                @foreach($fiscalYears as $fiscalYear)
                    <option value="{{ $fiscalYear->id }}" {{ (string) old('fiscal_year_id', $record->fiscal_year_id ?? $currentFiscalYear?->id) === (string) $fiscalYear->id ? 'selected' : '' }}>
                        {{ $fiscalYear->year }}{{ $fiscalYear->is_current ? ' (Current)' : '' }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('fiscal_year_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="creditor" value="Creditor *" />
            <x-input id="creditor" name="creditor" type="text" class="mt-1 block w-full" value="{{ old('creditor', $record->creditor ?? '') }}" required />
            <x-input-error :messages="$errors->get('creditor')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="date_contracted" value="Date Contracted *" />
            <x-input id="date_contracted" name="date_contracted" type="date" class="mt-1 block w-full" value="{{ old('date_contracted', isset($record) && $record->date_contracted ? $record->date_contracted->format('Y-m-d') : '') }}" required />
            <x-input-error :messages="$errors->get('date_contracted')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="term_maturity" value="Term/Maturity *" />
            <x-input id="term_maturity" name="term_maturity" type="text" class="mt-1 block w-full" value="{{ old('term_maturity', $record->term_maturity ?? '') }}" placeholder="e.g., 5 years / Dec 2030" required />
            <x-input-error :messages="$errors->get('term_maturity')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="principal_amount" value="Principal Amount *" />
            <x-input id="principal_amount" name="principal_amount" type="number" step="0.01" min="0" class="mt-1 block w-full amount-field" value="{{ old('principal_amount', $record->principal_amount ?? 0) }}" required />
            <x-input-error :messages="$errors->get('principal_amount')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="purpose" value="Purpose *" />
            <x-input id="purpose" name="purpose" type="text" class="mt-1 block w-full" value="{{ old('purpose', $record->purpose ?? '') }}" required />
            <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="prev_principal" value="Prev Principal" />
            <x-input id="prev_principal" name="prev_principal" type="number" step="0.01" min="0" class="mt-1 block w-full total-input" value="{{ old('prev_principal', $record->prev_principal ?? 0) }}" />
            <x-input-error :messages="$errors->get('prev_principal')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="prev_interest" value="Prev Interest" />
            <x-input id="prev_interest" name="prev_interest" type="number" step="0.01" min="0" class="mt-1 block w-full total-input" value="{{ old('prev_interest', $record->prev_interest ?? 0) }}" />
            <x-input-error :messages="$errors->get('prev_interest')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="prev_total" value="Prev Total *" />
            <x-input id="prev_total" name="prev_total" type="number" step="0.01" class="mt-1 block w-full bg-gray-50" value="{{ old('prev_total', $record->prev_total ?? 0) }}" readonly />
        </div>

        <div>
            <x-input-label for="due_principal" value="Due Principal *" />
            <x-input id="due_principal" name="due_principal" type="number" step="0.01" min="0" class="mt-1 block w-full due-input" value="{{ old('due_principal', $record->due_principal ?? 0) }}" required />
            <x-input-error :messages="$errors->get('due_principal')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="due_interest" value="Due Interest *" />
            <x-input id="due_interest" name="due_interest" type="number" step="0.01" min="0" class="mt-1 block w-full due-input" value="{{ old('due_interest', $record->due_interest ?? 0) }}" required />
            <x-input-error :messages="$errors->get('due_interest')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="due_total" value="Due Total" />
            <x-input id="due_total" name="due_total" type="number" step="0.01" class="mt-1 block w-full bg-gray-50" value="{{ old('due_total', $record->due_total ?? 0) }}" readonly />
        </div>

        <div>
            <x-input-label for="balance" value="Balance" />
            <x-input id="balance" name="balance" type="number" step="0.01" class="mt-1 block w-full bg-gray-50" value="{{ old('balance', $record->balance ?? 0) }}" readonly />
        </div>
    </div>

    <div class="flex justify-end gap-2 mt-8">
        <x-secondary-button onclick="window.location.href='{{ route('statements-of-indebtedness.index') }}'">
            Cancel
        </x-secondary-button>
        <x-primary-button type="submit">
            {{ $isEdit ? 'Update Record' : 'Create Record' }}
        </x-primary-button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const prevPrincipal = document.getElementById('prev_principal');
    const prevInterest = document.getElementById('prev_interest');
    const prevTotal = document.getElementById('prev_total');
    const duePrincipal = document.getElementById('due_principal');
    const dueInterest = document.getElementById('due_interest');
    const dueTotal = document.getElementById('due_total');
    const principalAmount = document.getElementById('principal_amount');
    const balance = document.getElementById('balance');

    const num = (el) => parseFloat(el?.value || 0) || 0;
    const set2 = (el, val) => { if (el) el.value = (Math.round(val * 100) / 100).toFixed(2); };

    function recalc() {
        set2(prevTotal, num(prevPrincipal) + num(prevInterest));
        set2(dueTotal, num(duePrincipal) + num(dueInterest));
        set2(balance, num(principalAmount) - num(duePrincipal));
    }

    [prevPrincipal, prevInterest, duePrincipal, dueInterest, principalAmount].forEach((el) => {
        if (el) el.addEventListener('input', recalc);
    });

    recalc();
});
</script>
