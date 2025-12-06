<x-app-layout>
    @section('title', 'Claimant Payees')

    <x-dashboard-header 
        title="Claimant Payees" 
        subtitle="Manage claimant payees and their information." 
    />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Claimant Payees</h2>
                <p class="text-sm text-gray-600 mt-1">Manage payee information and assignments</p>
            </div>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Payee
            </button>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Payees Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table id="payeesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($claimantPayees as $payee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payee->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ Str::limit($payee->address, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payee->department->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $payee->payeeCategory->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="editPayee({{ $payee->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button onclick="deletePayee({{ $payee->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No claimant payees found. Click "Add Payee" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="payeeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add Payee</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="payeeForm" method="POST" action="{{ route('claimant-payees.store') }}">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                <input type="hidden" id="payeeId" name="payee_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="name" value="Payee Name" />
                        <x-input id="name" type="text" name="name" required class="mt-1 block w-full" />
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="payee_category_id" value="Category" />
                        <select id="payee_category_id" name="payee_category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            @foreach($payeeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('payee_category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <x-input-label for="department_id" value="Department" />
                    <select id="department_id" name="department_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal 
        id="deletePayeeModal"
        title="Delete Payee"
        message="Are you sure you want to delete this payee? This action cannot be undone."
        confirmText="Delete"
        cancelText="Cancel"
        confirmClass="bg-red-600 hover:bg-red-700"
        icon="warning"
    />

    @push('scripts')
    <script>
        let payees = @json($claimantPayees);
        let deleteId = null;

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add Payee';
            document.getElementById('payeeForm').action = '{{ route("claimant-payees.store") }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('payeeId').value = '';
            document.getElementById('name').value = '';
            document.getElementById('address').value = '';
            document.getElementById('department_id').value = '';
            document.getElementById('payee_category_id').value = '';
            document.getElementById('payeeModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('payeeModal').classList.add('hidden');
        }

        function editPayee(id) {
            const payee = payees.find(p => p.id === id);
            if (!payee) return;

            document.getElementById('modalTitle').textContent = 'Edit Payee';
            document.getElementById('payeeForm').action = `/claimant-payees/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('payeeId').value = id;
            document.getElementById('name').value = payee.name;
            document.getElementById('address').value = payee.address;
            document.getElementById('department_id').value = payee.department_id;
            document.getElementById('payee_category_id').value = payee.payee_category_id;
            document.getElementById('payeeModal').classList.remove('hidden');
        }

        function deletePayee(id) {
            deleteId = id;
            showConfirmation({
                title: 'Delete Payee',
                message: 'Are you sure you want to delete this payee? This action cannot be undone.',
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/claimant-payees/${deleteId}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Initialize DataTable if there are payees
        @if($claimantPayees->count() > 0)
        $(document).ready(function() {
            $('#payeesTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 10,
                language: {
                    search: "Search payees:"
                }
            });
        });
        @endif
    </script>
    @endpush
</x-app-layout>
