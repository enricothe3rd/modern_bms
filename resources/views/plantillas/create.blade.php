<x-app-layout>
    <x-dashboard-header title="Create Plantilla" subtitle="Create department plantilla group with salary comparisons." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('plantillas.store') }}" method="POST">
            @csrf
            @include('plantillas._form')
        </form>
    </div>
</x-app-layout>
