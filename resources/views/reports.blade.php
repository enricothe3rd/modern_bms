<x-app-layout>
    @section('title', 'Reports')

<x-dashboard-header
    title="Reports"
    subtitle="Here's your overview."
    button-text="Add New"
    button-id="openModal"
/>

<div id="modalOverlay" class="modal-overlay items-center justify-center">
        <div class="modal-content bg-slate-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-slate-800">
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-50">Add New Item</h2>
                <button id="closeModal" class="p-1 hover:bg-slate-800 rounded transition">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                    <input type="text" placeholder="Enter full name" class="w-full px-4 py-2 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                    <input type="email" placeholder="Enter email address" class="w-full px-4 py-2 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Amount</label>
                    <input type="number" placeholder="Enter amount" class="w-full px-4 py-2 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Category</label>
                    <select class="w-full px-4 py-2 rounded-lg">
                        <option>Select a category</option>
                        <option>Sales</option>
                        <option>Marketing</option>
                        <option>Support</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                    <textarea placeholder="Enter description" rows="4" class="w-full px-4 py-2 rounded-lg resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" id="cancelBtn" class="btn-secondary flex-1 px-4 py-2 rounded-lg font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary flex-1 px-4 py-2 rounded-lg font-medium transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
