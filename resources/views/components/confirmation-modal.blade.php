@props([
    'id' => 'confirmationModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmClass' => 'bg-red-600 hover:bg-red-700',
    'icon' => 'warning'
])

<!-- Confirmation Modal -->
<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[60] flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative transform transition-all">
        
        <!-- Modal Content -->
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4
                {{ $icon === 'warning' ? 'bg-red-100' : 'bg-blue-100' }}">
                @if($icon === 'warning')
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                @else
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
            </div>

            <!-- Title -->
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="{{ $id }}Title">
                {{ $title }}
            </h3>

            <!-- Message -->
            <p class="text-sm text-gray-500 mb-6" id="{{ $id }}Message">
                {{ $message }}
            </p>

            <!-- Buttons -->
            <div class="flex justify-center space-x-3">
                <button type="button" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition"
                    id="{{ $id }}Cancel">
                    {{ $cancelText }}
                </button>
                <button type="button" 
                    class="px-4 py-2 text-white rounded-md transition {{ $confirmClass }}"
                    id="{{ $id }}Confirm">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('{{ $id }}');
    const cancelBtn = document.getElementById('{{ $id }}Cancel');
    const confirmBtn = document.getElementById('{{ $id }}Confirm');
    const titleEl = document.getElementById('{{ $id }}Title');
    const messageEl = document.getElementById('{{ $id }}Message');

    let currentCallback = null;

    // Show modal function
    window.showConfirmation = function(options = {}) {
        const {
            title = '{{ $title }}',
            message = '{{ $message }}',
            onConfirm = null,
            onCancel = null
        } = options;

        titleEl.textContent = title;
        messageEl.textContent = message;
        currentCallback = onConfirm;

        modal.classList.remove('hidden');
        
        // Focus on cancel button for accessibility
        setTimeout(() => cancelBtn.focus(), 100);
    };

    // Hide modal function
    function hideModal() {
        modal.classList.add('hidden');
        currentCallback = null;
    }

    // Cancel button
    cancelBtn.addEventListener('click', hideModal);

    // Confirm button
    confirmBtn.addEventListener('click', function() {
        if (currentCallback && typeof currentCallback === 'function') {
            currentCallback();
        }
        hideModal();
    });

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });
});
</script>