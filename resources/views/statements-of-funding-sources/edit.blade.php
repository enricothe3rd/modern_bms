<x-app-layout>
    <x-dashboard-header title="Edit Statement of Funding Sources" subtitle="Update categories and funding source items." />
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('statements-of-funding-sources.update', $record) }}" method="POST">
            @csrf
            @method('PUT')
            @include('statements-of-funding-sources._form')
        </form>
    </div>
</x-app-layout>
