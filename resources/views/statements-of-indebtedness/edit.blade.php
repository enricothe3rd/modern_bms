<x-app-layout>
    <x-dashboard-header
        title="Edit Statement of Indebtedness"
        subtitle="Update indebtedness details."
    />

    <div class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <x-secondary-button onclick="window.location.href='{{ route('statements-of-indebtedness.index') }}'">
                Back to list
            </x-secondary-button>
        </div>

        <form action="{{ route('statements-of-indebtedness.update', $record) }}" method="POST">
            @csrf
            @method('PUT')
            @include('statements-of-indebtedness._form')
        </form>
    </div>
</x-app-layout>
