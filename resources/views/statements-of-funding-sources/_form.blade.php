@php
    $isEdit = isset($record);
    $currentFiscalYear = $currentFiscalYear ?? null;
    $oldCategories = old('categories');
    $oldItems = old('items');

    if ($oldCategories !== null) {
        $categoriesData = array_values($oldCategories);
    } elseif ($isEdit) {
        $categoriesData = $record->categories->map(fn($c) => [
            'category_number' => $c->category_number,
            'category_name' => $c->category_name,
            'amount' => $c->amount,
        ])->values()->all();
    } else {
        $categoriesData = [['category_number' => '', 'category_name' => '', 'amount' => '']];
    }

    if ($oldItems !== null) {
        $itemsData = array_values($oldItems);
    } elseif ($isEdit) {
        $itemsData = [];
        foreach ($record->categories->values() as $catIndex => $category) {
            foreach ($category->items as $item) {
                $itemsData[] = [
                    'category_ref' => $catIndex,
                    'particulars' => $item->particulars,
                    'account_classification' => $item->account_classification,
                    'amount' => $item->amount,
                ];
            }
        }
    } else {
        $itemsData = [];
    }
@endphp

@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
        <div class="font-semibold mb-2">Please fix the following errors:</div>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="fiscal_year_id" value="Fiscal Year *" />
            <select name="fiscal_year_id" id="fiscal_year_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1" required>
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
            <x-input-label for="title" value="Title (Optional)" />
            <x-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $record->title ?? '') }}" />
        </div>
    </div>
    <div class="mt-6">
        <x-input-label for="remarks" value="Remarks (Optional)" />
        <textarea name="remarks" id="remarks" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">{{ old('remarks', $record->remarks ?? '') }}</textarea>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Categories</h2>
        <x-secondary-button type="button" id="add-category-btn">Add Category</x-secondary-button>
    </div>
    <div id="categories-container" class="space-y-4"></div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Items</h2>
        <x-secondary-button type="button" id="add-item-btn">Add Item</x-secondary-button>
    </div>
    <p class="text-sm text-gray-500 mb-4">Select category from categories created above.</p>
    <div id="items-container" class="space-y-4"></div>
</div>

<div class="flex justify-end gap-2">
    <x-secondary-button onclick="window.location.href='{{ route('statements-of-funding-sources.index') }}'">Cancel</x-secondary-button>
    <x-primary-button type="submit">{{ $isEdit ? 'Update Record' : 'Create Record' }}</x-primary-button>
</div>

<template id="category-template">
    <div class="category-row border border-gray-200 rounded-lg p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-gray-800">Category <span class="category-label"></span></h3>
            <button type="button" class="remove-category text-red-600 text-sm">Remove</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label value="Category Number *" />
                <input type="text" class="category-number-input mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
            <div>
                <x-input-label value="Category Name *" />
                <input type="text" class="category-name-input mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
            <div>
                <x-input-label value="Amount (Optional)" />
                <input type="number" step="0.01" min="0" class="category-amount-input mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
        </div>
    </div>
</template>

<template id="item-template">
    <div class="item-row border border-gray-200 rounded-lg p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-gray-800">Item</h3>
            <button type="button" class="remove-item text-red-600 text-sm">Remove</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label value="Category *" />
                <select class="item-category-select mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2"></select>
            </div>
            <div>
                <x-input-label value="Amount (Optional)" />
                <input type="number" step="0.01" min="0" class="item-amount-input mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
            <div>
                <x-input-label value="Particulars *" />
                <input type="text" class="item-particulars-input mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
            <div>
                <x-input-label value="Account Classification (Optional)" />
                <input type="text" class="item-account-classification-input mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoriesContainer = document.getElementById('categories-container');
    const itemsContainer = document.getElementById('items-container');
    const categoryTemplate = document.getElementById('category-template');
    const itemTemplate = document.getElementById('item-template');
    const initialCategories = @json($categoriesData);
    const initialItems = @json($itemsData);

    function categoryRows() { return Array.from(categoriesContainer.querySelectorAll('.category-row')); }
    function itemRows() { return Array.from(itemsContainer.querySelectorAll('.item-row')); }

    function labels() {
        return categoryRows().map((row, i) => {
            const num = row.querySelector('.category-number-input').value || ('Category ' + (i + 1));
            const name = row.querySelector('.category-name-input').value || '';
            return { value: String(i), label: name ? `${num} - ${name}` : num };
        });
    }

    function refreshItemCategoryOptions() {
        const opts = labels();
        itemRows().forEach((row) => {
            const select = row.querySelector('.item-category-select');
            const selected = select.dataset.selectedValue ?? select.value;
            select.innerHTML = '';
            if (!opts.length) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Add categories first';
                select.appendChild(option);
                return;
            }
            opts.forEach((opt) => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                if (String(selected) === String(opt.value)) option.selected = true;
                select.appendChild(option);
            });
        });
    }

    function rebuildNames() {
        categoryRows().forEach((row, index) => {
            row.querySelector('.category-label').textContent = `#${index + 1}`;
            row.querySelector('.category-number-input').name = `categories[${index}][category_number]`;
            row.querySelector('.category-name-input').name = `categories[${index}][category_name]`;
            row.querySelector('.category-amount-input').name = `categories[${index}][amount]`;
        });

        itemRows().forEach((row, index) => {
            row.querySelector('.item-category-select').name = `items[${index}][category_ref]`;
            row.querySelector('.item-particulars-input').name = `items[${index}][particulars]`;
            row.querySelector('.item-account-classification-input').name = `items[${index}][account_classification]`;
            row.querySelector('.item-amount-input').name = `items[${index}][amount]`;
        });

        refreshItemCategoryOptions();
    }

    function addCategory(data = {}) {
        const node = categoryTemplate.content.cloneNode(true);
        const row = node.querySelector('.category-row');
        row.querySelector('.category-number-input').value = data.category_number ?? '';
        row.querySelector('.category-name-input').value = data.category_name ?? '';
        row.querySelector('.category-amount-input').value = data.amount ?? '';
        row.querySelector('.remove-category').addEventListener('click', () => { row.remove(); rebuildNames(); });
        row.querySelector('.category-number-input').addEventListener('input', refreshItemCategoryOptions);
        row.querySelector('.category-name-input').addEventListener('input', refreshItemCategoryOptions);
        categoriesContainer.appendChild(row);
        rebuildNames();
    }

    function addItem(data = {}) {
        if (!categoryRows().length) {
            alert('Add at least one category first.');
            return;
        }
        const node = itemTemplate.content.cloneNode(true);
        const row = node.querySelector('.item-row');
        row.querySelector('.item-category-select').dataset.selectedValue = String(data.category_ref ?? 0);
        row.querySelector('.item-particulars-input').value = data.particulars ?? '';
        row.querySelector('.item-account-classification-input').value = data.account_classification ?? '';
        row.querySelector('.item-amount-input').value = data.amount ?? '';
        row.querySelector('.remove-item').addEventListener('click', () => { row.remove(); rebuildNames(); });
        itemsContainer.appendChild(row);
        rebuildNames();
    }

    document.getElementById('add-category-btn').addEventListener('click', () => addCategory());
    document.getElementById('add-item-btn').addEventListener('click', () => addItem());

    initialCategories.forEach(addCategory);
    if (!initialCategories.length) addCategory();
    initialItems.forEach(addItem);
});
</script>
