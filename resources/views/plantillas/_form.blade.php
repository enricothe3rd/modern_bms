@php
    $currentFiscalYear = $currentFiscalYear ?? null;
    $record = $record ?? null;

    $salarySchedulePayload = $salarySchedules->map(function ($schedule) {
        $cells = [];
        foreach ($schedule->cells as $cell) {
            $key = $cell->salary_grade . '-' . $cell->step_no;
            $cells[$key] = [
                'label' => 'SG ' . $cell->salary_grade . ' - Step ' . $cell->step_no,
                'amount' => (float) $cell->amount,
            ];
        }
        return [
            'id' => $schedule->id,
            'label' => ($schedule->title ?: ('Salary Schedule #' . $schedule->id)) . ' (' . optional($schedule->effective_date)->format('M d, Y') . ')',
            'cells' => $cells,
        ];
    })->values();

    if (old('items')) {
        $itemsData = array_values(old('items'));
    } elseif ($record) {
        $itemsData = $record->items->map(function ($item) {
            $toGroups = [];
            $fromGroups = [];

            if ($item->relationLoaded('groups') && $item->groups->isNotEmpty()) {
                $toGroups = $item->groups
                    ->where('group_type', 'to')
                    ->values()
                    ->map(function ($group) {
                        return [
                            'group_label' => $group->group_label,
                            'movements' => $group->movements->map(function ($mv) {
                                return [
                                    'label' => $mv->label,
                                    'salary_schedule_id' => $mv->salary_schedule_id,
                                    'salary_cell' => ($mv->salary_grade && $mv->salary_step) ? ($mv->salary_grade . '-' . $mv->salary_step) : '',
                                ];
                            })->values()->all(),
                        ];
                    })->all();

                $fromGroups = $item->groups
                    ->where('group_type', 'from')
                    ->values()
                    ->map(function ($group) {
                        return [
                            'group_label' => $group->group_label,
                            'movements' => $group->movements->map(function ($mv) {
                                return [
                                    'label' => $mv->label,
                                    'salary_schedule_id' => $mv->salary_schedule_id,
                                    'salary_cell' => ($mv->salary_grade && $mv->salary_step) ? ($mv->salary_grade . '-' . $mv->salary_step) : '',
                                ];
                            })->values()->all(),
                        ];
                    })->all();
            } elseif ($item->relationLoaded('movements') && $item->movements->isNotEmpty()) {
                // Legacy fallback: convert old combined movement rows into one-entry groups for each side.
                $toGroups = $item->movements->values()->map(function ($mv, $idx) {
                    return [
                        'group_label' => 'Salary To Group ' . ($idx + 1),
                        'movements' => [[
                            'label' => $mv->label,
                            'salary_schedule_id' => $mv->salary_schedule_to_id,
                            'salary_cell' => ($mv->salary_to_grade && $mv->salary_to_step) ? ($mv->salary_to_grade . '-' . $mv->salary_to_step) : '',
                        ]],
                    ];
                })->all();
                $fromGroups = $item->movements->values()->map(function ($mv, $idx) {
                    return [
                        'group_label' => 'Salary From Group ' . ($idx + 1),
                        'movements' => [[
                            'label' => $mv->label,
                            'salary_schedule_id' => $mv->salary_schedule_from_id,
                            'salary_cell' => ($mv->salary_from_grade && $mv->salary_from_step) ? ($mv->salary_from_grade . '-' . $mv->salary_from_step) : '',
                        ]],
                    ];
                })->all();
            }

            return [
                'employee_name' => $item->employee_name,
                'position' => $item->position,
                'old_count' => $item->old_count,
                'new_count' => $item->new_count,
                'to_groups' => $toGroups,
                'from_groups' => $fromGroups,
            ];
        })->values()->all();
    } else {
        $itemsData = [[
            'employee_name' => '',
            'position' => '',
            'old_count' => 0,
            'new_count' => 0,
            'to_groups' => [[
                'group_label' => 'Salary To Group 1',
                'movements' => [[
                    'label' => '',
                    'salary_schedule_id' => '',
                    'salary_cell' => '',
                ]],
            ]],
            'from_groups' => [[
                'group_label' => 'Salary From Group 1',
                'movements' => [[
                    'label' => '1st Tranche',
                    'salary_schedule_id' => '',
                    'salary_cell' => '',
                ]],
            ]],
        ]];
    }
@endphp

@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 text-red-900 px-4 py-3 mb-6 rounded">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
            <x-input-label for="department_id" value="Department *" />
            <select name="department_id" id="department_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1" required>
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ (string) old('department_id', $record->department_id ?? '') === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="group_name" value="Group Name *" />
            <x-input id="group_name" name="group_name" type="text" class="mt-1 block w-full" value="{{ old('group_name', $record->group_name ?? '') }}" required />
        </div>
        <div>
            <x-input-label for="fiscal_year_id" value="Fiscal Year" />
            <select name="fiscal_year_id" id="fiscal_year_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">
                <option value="">Select Fiscal Year</option>
                @foreach($fiscalYears as $fiscalYear)
                    <option value="{{ $fiscalYear->id }}" {{ (string) old('fiscal_year_id', $record->fiscal_year_id ?? $currentFiscalYear?->id) === (string) $fiscalYear->id ? 'selected' : '' }}>{{ $fiscalYear->year }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label value="Computed Grand Total" />
            <input id="grand_total_preview" type="text" class="mt-1 block w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-right" value="0.00" readonly />
        </div>
    </div>
    <div class="mt-6">
        <x-input-label for="notes" value="Notes" />
        <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1">{{ old('notes', $record->notes ?? '') }}</textarea>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Employees</h2>
        <x-secondary-button type="button" id="add-employee-btn">Add Employee</x-secondary-button>
    </div>
    <div id="employees-container" class="space-y-6"></div>
</div>

<div class="flex justify-end gap-2">
    <x-secondary-button onclick="window.location.href='{{ route('plantillas.index') }}'">Cancel</x-secondary-button>
    <x-primary-button type="submit">{{ $record ? 'Update Plantilla' : 'Create Plantilla' }}</x-primary-button>
</div>

<template id="employee-template">
    <div class="employee-row border border-gray-200 rounded-xl p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-gray-800">Employee <span class="employee-seq"></span></h3>
            <button type="button" class="remove-employee text-red-600 text-sm">Remove Employee</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
            <div>
                <x-input-label value="Name *" />
                <input type="text" class="emp-name mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
            <div>
                <x-input-label value="Position *" />
                <input type="text" class="emp-position mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2" />
            </div>
            <div>
                <x-input-label value="Old *" />
                <input type="number" min="0" class="emp-old mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 text-right" />
            </div>
            <div>
                <x-input-label value="New *" />
                <input type="number" min="0" class="emp-new mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 text-right" />
            </div>
            <div>
                <x-input-label value="Status" />
                <input type="text" class="emp-indicator mt-1 block w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50" readonly />
            </div>
            <div>
                <x-input-label value="Employee Total" />
                <input type="text" class="emp-total mt-1 block w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-right" readonly />
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                <div class="mb-3">
                    <h4 class="font-medium text-gray-700">Salary To Group</h4>
                </div>
                <div class="to-groups-container space-y-3"></div>
            </div>
            <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                <div class="mb-3">
                    <h4 class="font-medium text-gray-700">Salary From Group</h4>
                </div>
                <div class="from-groups-container space-y-3"></div>
            </div>
        </div>
    </div>
</template>

<template id="group-template">
    <div class="salary-group-row border border-gray-200 rounded-xl p-3 bg-white">
        <div class="flex justify-between items-center mb-3">
            <div class="text-sm font-semibold text-gray-700 group-title">Group</div>
            <span class="text-xs text-gray-500">One group only</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
            <div>
                <x-input-label value="Group Label" />
                <input type="text" class="group-label mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2" />
            </div>
            <div>
                <x-input-label value="Group Total" />
                <input type="text" class="group-total mt-1 block w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-right" readonly />
            </div>
            <div class="flex items-end">
                <button type="button" class="add-movement text-sm px-3 py-2 rounded bg-white border border-gray-300 hover:bg-gray-100">Add Movement</button>
            </div>
        </div>
        <div class="movements-container space-y-2"></div>
    </div>
</template>

<template id="movement-template">
    <div class="movement-row border border-gray-200 rounded-lg p-3 bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <x-input-label value="Label" />
                <input type="text" class="mv-label mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="e.g. 1st Tranche" />
            </div>
            <div>
                <x-input-label value="Schedule" />
                <select class="mv-sched mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2"></select>
            </div>
            <div>
                <x-input-label value="Salary Cell" />
                <select class="mv-cell mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2"></select>
            </div>
            <div>
                <x-input-label value="Amount" />
                <input type="text" class="mv-amount mt-1 block w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-right" readonly />
            </div>
            <div class="flex items-end">
                <button type="button" class="remove-movement text-red-600 text-sm">Remove Movement</button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const employeesContainer = document.getElementById('employees-container');
    const employeeTpl = document.getElementById('employee-template');
    const groupTpl = document.getElementById('group-template');
    const movementTpl = document.getElementById('movement-template');
    const addEmployeeBtn = document.getElementById('add-employee-btn');
    const schedules = @json($salarySchedulePayload);
    const initialItems = @json($itemsData);

    const scheduleMap = Object.fromEntries(schedules.map(s => [String(s.id), s]));
    const money = (n) => (Math.round((Number(n) || 0) * 100) / 100).toFixed(2);

    const employeeRows = () => Array.from(employeesContainer.querySelectorAll('.employee-row'));
        const toGroupRows = (empRow) => Array.from(empRow.querySelectorAll('.to-groups-container .salary-group-row'));
        const fromGroupRows = (empRow) => Array.from(empRow.querySelectorAll('.from-groups-container .salary-group-row'));
        const movementRows = (groupRow) => Array.from(groupRow.querySelectorAll('.movement-row'));

    function buildScheduleOptions(select, selected = '') {
        select.innerHTML = '<option value="">Select Schedule</option>';
        schedules.forEach(s => {
            const o = document.createElement('option');
            o.value = s.id;
            o.textContent = s.label;
            if (String(selected) === String(s.id)) o.selected = true;
            select.appendChild(o);
        });
    }

    function buildCellOptions(select, scheduleId, selected = '') {
        select.innerHTML = '<option value="">Select Salary Cell</option>';
        const schedule = scheduleMap[String(scheduleId)];
        if (!schedule) return;
        Object.entries(schedule.cells).forEach(([key, meta]) => {
            const o = document.createElement('option');
            o.value = key;
            o.textContent = `${meta.label} (${money(meta.amount)})`;
            if (String(selected) === String(key)) o.selected = true;
            select.appendChild(o);
        });
    }

    function amountFor(scheduleId, cellKey) {
        const schedule = scheduleMap[String(scheduleId)];
        if (!schedule || !cellKey) return 0;
        return Number(schedule.cells[cellKey]?.amount || 0);
    }

    function groupType(groupRow) {
        return groupRow.dataset.groupType;
    }

    function recomputeMovement(empRow, groupRow, mvRow) {
        const sched = mvRow.querySelector('.mv-sched').value;
        const cell = mvRow.querySelector('.mv-cell').value;
        const amount = amountFor(sched, cell);
        const total = (sched && cell) ? amount : 0;

        mvRow.querySelector('.mv-amount').value = money(amount);
        mvRow.dataset.movementTotal = String(total);
        recomputeGroupTotal(empRow, groupRow);
    }

    function recomputeGroupTotal(empRow, groupRow) {
        let sum = 0;
        movementRows(groupRow).forEach(row => sum += Number(row.dataset.movementTotal || 0));
        groupRow.querySelector('.group-total').value = money(sum);
        recomputeEmployeeTotal(empRow);
    }

    function recomputeEmployeeTotal(empRow) {
        let toTotal = 0;
        let fromTotal = 0;
        toGroupRows(empRow).forEach(group => toTotal += Number(group.querySelector('.group-total').value || 0));
        fromGroupRows(empRow).forEach(group => fromTotal += Number(group.querySelector('.group-total').value || 0));
        const rawDiff = toTotal - fromTotal;
        const indicator = empRow.querySelector('.emp-indicator');
        indicator.value = rawDiff > 0 ? 'Increase' : (rawDiff < 0 ? 'Decrease' : 'No Change');
        indicator.className = 'emp-indicator mt-1 block w-full border rounded-lg px-4 py-2 bg-gray-50';
        if (rawDiff > 0) {
            indicator.classList.add('border-green-200', 'text-green-700', 'bg-green-50');
        } else if (rawDiff < 0) {
            indicator.classList.add('border-red-200', 'text-red-700', 'bg-red-50');
        } else {
            indicator.classList.add('border-gray-200', 'text-gray-700', 'bg-gray-50');
        }

        empRow.querySelector('.emp-total').value = money(Math.abs(rawDiff));
        recomputeGrandTotal();
    }

    function recomputeGrandTotal() {
        let total = 0;
        employeeRows().forEach(emp => total += Number(emp.querySelector('.emp-total').value || 0));
        document.getElementById('grand_total_preview').value = money(total);
    }

    function rebuildNames() {
        employeeRows().forEach((empRow, i) => {
            empRow.querySelector('.employee-seq').textContent = `#${i + 1}`;
            empRow.querySelector('.emp-name').name = `items[${i}][employee_name]`;
            empRow.querySelector('.emp-position').name = `items[${i}][position]`;
            empRow.querySelector('.emp-old').name = `items[${i}][old_count]`;
            empRow.querySelector('.emp-new').name = `items[${i}][new_count]`;

            [['to', toGroupRows(empRow)], ['from', fromGroupRows(empRow)]].forEach(([type, groups]) => {
                groups.forEach((groupRow, g) => {
                    const defaultLabel = type === 'to' ? `Salary To Group ${g + 1}` : `Salary From Group ${g + 1}`;
                    const title = groupRow.querySelector('.group-title');
                    title.textContent = defaultLabel;
                    const labelInput = groupRow.querySelector('.group-label');
                    if (!labelInput.value) {
                        labelInput.value = defaultLabel;
                    }
                    labelInput.name = `items[${i}][${type}_groups][${g}][group_label]`;

                    movementRows(groupRow).forEach((mvRow, j) => {
                        mvRow.querySelector('.mv-label').name = `items[${i}][${type}_groups][${g}][movements][${j}][label]`;
                        mvRow.querySelector('.mv-sched').name = `items[${i}][${type}_groups][${g}][movements][${j}][salary_schedule_id]`;
                        mvRow.querySelector('.mv-cell').name = `items[${i}][${type}_groups][${g}][movements][${j}][salary_cell]`;
                    });
                });
            });
        });
        recomputeGrandTotal();
    }

    function addMovement(empRow, groupRow, data = {}) {
        const node = movementTpl.content.cloneNode(true);
        const mvRow = node.querySelector('.movement-row');
        const sched = mvRow.querySelector('.mv-sched');
        const cell = mvRow.querySelector('.mv-cell');

        mvRow.querySelector('.mv-label').value = data.label ?? '';
        buildScheduleOptions(sched, data.salary_schedule_id ?? '');
        buildCellOptions(cell, sched.value, data.salary_cell ?? '');

        sched.addEventListener('change', () => { buildCellOptions(cell, sched.value, ''); recomputeMovement(empRow, groupRow, mvRow); });
        cell.addEventListener('change', () => recomputeMovement(empRow, groupRow, mvRow));
        mvRow.querySelector('.remove-movement').addEventListener('click', () => {
            mvRow.remove();
            rebuildNames();
            recomputeGroupTotal(empRow, groupRow);
        });

        groupRow.querySelector('.movements-container').appendChild(mvRow);
        rebuildNames();
        recomputeMovement(empRow, groupRow, mvRow);
    }

    function addGroup(empRow, containerSelector, type, data = {}) {
        const node = groupTpl.content.cloneNode(true);
        const groupRow = node.querySelector('.salary-group-row');
        groupRow.dataset.groupType = type;
        groupRow.querySelector('.group-label').value = data.group_label ?? '';
        groupRow.querySelector('.add-movement').textContent = type === 'to' ? 'Add Salary To Movement' : 'Add Salary From Movement';

        groupRow.querySelector('.add-movement').addEventListener('click', () => addMovement(empRow, groupRow, { label: '' }));

        empRow.querySelector(containerSelector).appendChild(groupRow);
        const rows = Array.isArray(data.movements) ? data.movements : [];
        rows.forEach(row => addMovement(empRow, groupRow, row));
        rebuildNames();
        recomputeGroupTotal(empRow, groupRow);
    }

    function addEmployee(data = {}) {
        const node = employeeTpl.content.cloneNode(true);
        const empRow = node.querySelector('.employee-row');
        empRow.querySelector('.emp-name').value = data.employee_name ?? '';
        empRow.querySelector('.emp-position').value = data.position ?? '';
        empRow.querySelector('.emp-old').value = data.old_count ?? 0;
        empRow.querySelector('.emp-new').value = data.new_count ?? 0;

        empRow.querySelector('.remove-employee').addEventListener('click', () => {
            empRow.remove();
            rebuildNames();
        });
        employeesContainer.appendChild(empRow);

        const toGroups = Array.isArray(data.to_groups) && data.to_groups.length ? data.to_groups : [{ group_label: 'Salary To Group', movements: [{ label: '' }] }];
        const fromGroups = Array.isArray(data.from_groups) && data.from_groups.length ? data.from_groups : [{ group_label: 'Salary From Group', movements: [{ label: '' }] }];

        addGroup(empRow, '.to-groups-container', 'to', toGroups[0]);
        addGroup(empRow, '.from-groups-container', 'from', fromGroups[0]);

        rebuildNames();
        recomputeEmployeeTotal(empRow);
    }

    addEmployeeBtn.addEventListener('click', () => addEmployee({
        to_groups: [{ group_label: 'Salary To Group', movements: [{ label: '' }] }],
        from_groups: [{ group_label: 'Salary From Group', movements: [{ label: '' }] }],
    }));
    initialItems.forEach(addEmployee);
    if (!initialItems.length) addEmployee({
        to_groups: [{ group_label: 'Salary To Group', movements: [{ label: '' }] }],
        from_groups: [{ group_label: 'Salary From Group', movements: [{ label: '1st Tranche' }] }],
    });
});
</script>
