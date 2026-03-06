<x-app-layout>
    <x-dashboard-header title="Create Salary Schedule" subtitle="Enter salary amounts in grid format." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('salary-schedules.store') }}" method="POST">
            @csrf
            @include('salary-schedules._form', ['grid' => $defaultGrid])
        </form>
    </div>
</x-app-layout>
