<x-app-layout>
    <x-dashboard-header title="Create Statement of Funding Sources" subtitle="Add categories and funding source items." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('statements-of-funding-sources.store') }}" method="POST">
            @csrf
            @include('statements-of-funding-sources._form')
        </form>
    </div>
</x-app-layout>
