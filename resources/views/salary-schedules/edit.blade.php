<x-app-layout>
    <x-dashboard-header title="Edit Salary Schedule" subtitle="Update salary grade and step amounts." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('salary-schedules.update', $record) }}" method="POST">
            @csrf
            @method('PUT')
            @include('salary-schedules._form')
        </form>
    </div>
</x-app-layout>
