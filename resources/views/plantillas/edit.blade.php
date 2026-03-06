<x-app-layout>
    <x-dashboard-header title="Edit Plantilla" subtitle="Update plantilla items and salary mapping." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('plantillas.update', $record) }}" method="POST">
            @csrf
            @method('PUT')
            @include('plantillas._form')
        </form>
    </div>
</x-app-layout>
