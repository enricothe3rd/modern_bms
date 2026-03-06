<x-app-layout>
    <x-dashboard-header title="Create Statement of Statutory Obligations" subtitle="Add categories and statutory obligation items." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('statements-of-statutory-obligations.store') }}" method="POST">
            @csrf
            @include('statements-of-statutory-obligations._form')
        </form>
    </div>
</x-app-layout>
