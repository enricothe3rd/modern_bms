<x-app-layout>
    <x-dashboard-header title="Edit Statement of Statutory Obligations" subtitle="Update categories and items." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('statements-of-statutory-obligations.update', $record) }}" method="POST">
            @csrf
            @method('PUT')
            @include('statements-of-statutory-obligations._form')
        </form>
    </div>
</x-app-layout>
