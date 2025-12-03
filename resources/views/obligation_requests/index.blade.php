<x-app-layout>
    @section('title', 'Obligations')

<x-dashboard-header
    title="Obligations"
    subtitle="Here's your overview."
    button-text="Add New"
    button-id="openModal"
/>


<div class="max-w-3xl mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100 mt-4">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-6">User Registration</h2>
    <form>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="grid-first-name">
                    First Name
                </label>
                <input class="block w-full text-gray-800 border-gray-300 rounded-lg shadow-sm py-2.5 px-4
                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              transition duration-150"
                       id="grid-first-name" type="text" placeholder="Jane">
                <p class="text-red-500 text-xs mt-1.5">Please fill out this field.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="grid-last-name">
                    Last Name
                </label>
                <input class="block w-full text-gray-800 border-gray-300 rounded-lg shadow-sm py-2.5 px-4
                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              transition duration-150"
                       id="grid-last-name" type="text" placeholder="Doe">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="grid-password">
                Password
            </label>
            <input class="block w-full text-gray-800 border-gray-300 rounded-lg shadow-sm py-2.5 px-4
                          focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                          transition duration-150"
                   id="grid-password" type="password" placeholder="••••••••••••••••••">
            <p class="text-gray-500 text-xs mt-1.5">Make it as long and as crazy as you'd like</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="grid-city">
                    City
                </label>
                <input class="block w-full text-gray-800 border-gray-300 rounded-lg shadow-sm py-2.5 px-4
                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              transition duration-150"
                       id="grid-city" type="text" placeholder="Albuquerque">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="grid-state">
                    State
                </label>
                <div class="relative">
                    <select class="block appearance-none w-full text-gray-800 border-gray-300 rounded-lg shadow-sm py-2.5 px-4 pr-10
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                   transition duration-150"
                            id="grid-state">
                        <option>New Mexico</option>
                        <option>Missouri</option>
                        <option>Texas</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="grid-zip">
                    Zip
                </label>
                <input class="block w-full text-gray-800 border-gray-300 rounded-lg shadow-sm py-2.5 px-4
                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              transition duration-150"
                       id="grid-zip" type="text" placeholder="90210">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-md
                           hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/50
                           transition duration-150">
                Create Account
            </button>
        </div>
    </form>
</div>


</x-app-layout>
