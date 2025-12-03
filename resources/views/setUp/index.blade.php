<x-app-layout>
    @section('title', 'Set Up')

<x-dashboard-header
    title="Set up"
    subtitle="Here's your overview."
/>


<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <header class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight border-b-2 border-indigo-500 pb-2">
            ⚙️ Master Data Configuration
        </h1>
        <p class="mt-2 text-lg text-gray-500">
            Manage core lookup tables and settings for the application.
        </p>
    </header>

   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- 1. Fund Types Setup Card --}}
        <x-setup-card
            title="Fund Types"
            description="Define and manage categories for different funding sources."
            :route="route('setUp')" {{-- Assumes you have specific routes now --}}
            color="indigo"
        >
            <x-slot name="icon">
                {{-- Icon for Fund Types --}}
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m-3.5-6h6m-2-4h-2m4 0h-2m-2 4h-2m4 0h-2m-2 4h-2m4 0h-2m-2 4h-2m4 0h-2m-2 4h-2m4 0h-2"></path></svg>
            </x-slot>
        </x-setup-card>

        {{-- 2. Expense Types Setup Card --}}
        <x-setup-card
            title="Expense Types"
            description="Classify expenditures for reporting and budgeting purposes."
            :route="route('setUp')"
            color="teal"
        >
            <x-slot name="icon">
                {{-- Icon for Expense Types --}}
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6a2 2 0 00-2-2H5a2 2 0 00-2 2v13a2 2 0 002 2h4zm0 0a2 2 0 012-2h2a2 2 0 012 2m-9 0h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4 0V3m4 2V3m-2 4h.01"></path></svg>
            </x-slot>
        </x-setup-card>

        {{-- 3. Sectors Setup Card (Departments/Divisions) --}}
        <x-setup-card
            title="Sectors / Departments"
            description="Organize and define the different operational divisions."
            :route="route('setUp')"
            color="yellow"
        >
            <x-slot name="icon">
                {{-- Icon for Sectors/Departments --}}
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-4a1 1 0 011-1h2a1 1 0 011 1v4"></path></svg>
            </x-slot>
        </x-setup-card>

        {{-- 4. Locations/Branches Setup Card --}}
        <x-setup-card
            title="Locations / Branches"
            description="Define physical operating locations or branch offices."
            :route="route('setUp')"
            color="red"
        >
            <x-slot name="icon">
                {{-- Icon for Locations --}}
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </x-slot>
        </x-setup-card>

        {{-- 5. Vendor/Supplier Setup Card --}}
        <x-setup-card
            title="Vendors / Suppliers"
            description="Maintain a list of all external business partners."
            :route="route('setUp')"
            color="pink"
        >
            <x-slot name="icon">
                {{-- Icon for Vendors --}}
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </x-slot>
        </x-setup-card>

        {{-- 6. User Roles & Permissions Setup Card --}}
        <x-setup-card
            title="User Roles & Access"
            description="Manage user roles and system access permissions."
            :route="route('setUp')"
            color="purple"
        >
            <x-slot name="icon">
                {{-- Icon for Roles/Permissions --}}
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m-1.5 5.5h7m-3.5 0a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"></path></svg>
            </x-slot>
        </x-setup-card>


    </div>
</div>

</x-app-layout>
