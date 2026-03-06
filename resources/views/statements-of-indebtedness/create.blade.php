<x-app-layout>
    <x-dashboard-header
        title="Create Statement of Indebtedness"
        subtitle="Add a new indebtedness record."
    />

    <div class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <x-secondary-button onclick="window.location.href='{{ route('statements-of-indebtedness.index') }}'">
                Back to list
            </x-secondary-button>
        </div>

        <form action="{{ route('statements-of-indebtedness.store') }}" method="POST">
            @csrf
            @include('statements-of-indebtedness._form')
        </form>
    </div>
</x-app-layout>
