<x-app-layout>
<main class="flex-1 overflow-auto bg-slate-50">
        <x-dashboard-header
            title="Dashboard"
            subtitle="Here's your overview."
        />

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <x-dashboard-card
                    label="Total Revenue"
                    value="$45,231"
                    change="↑ 12% from last month"
                    change-color="cyan"
                    icon-bg-class="bg-gradient-to-br from-purple-500 to-cyan-500"
                />

                <x-dashboard-card
                    label="New Users"
                    value="1,234"
                    change="↓ 5% from last month"
                    change-color="red"
                    icon-bg-class="bg-gradient-to-br from-green-500 to-blue-500"
                />

                <x-dashboard-card
                    label="Net Profit"
                    value="$12,430"
                />
                <x-dashboard-card
                    label="New Users"
                    value="1,234"
                    change="↓ 5% from last month"
                    change-color="red"
                    icon-bg-class="bg-gradient-to-br from-green-500 to-blue-500"
                />

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 metric-card p-6 rounded-xl border bg-white shadow-md">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Revenue Overview</h3>
                    <p class="text-slate-600 text-sm mt-1">Last 12 months</p>
                </div>
                <div class="h-64 flex items-end justify-around gap-2">
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-gradient-to-t from-purple-500 to-purple-600 rounded-t" style="height: 60%;"></div>
                        <span class="text-xs text-slate-600">Jan</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-gradient-to-t from-purple-500 to-purple-600 rounded-t" style="height: 75%;"></div>
                        <span class="text-xs text-slate-600">Feb</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-gradient-to-t from-cyan-500 to-cyan-600 rounded-t" style="height: 85%;"></div>
                        <span class="text-xs text-slate-600">Mar</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-gradient-to-t from-cyan-500 to-cyan-600 rounded-t" style="height: 70%;"></div>
                        <span class="text-xs text-slate-600">Apr</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-gradient-to-t from-purple-500 to-purple-600 rounded-t" style="height: 80%;"></div>
                        <span class="text-xs text-slate-600">May</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-gradient-to-t from-purple-500 to-purple-600 rounded-t" style="height: 90%;"></div>
                        <span class="text-xs text-slate-600">Jun</span>
                    </div>
                </div>
            </div>

            <div class="metric-card p-6 rounded-xl border bg-white shadow-md">
                <h3 class="text-lg font-semibold text-slate-900 mb-6">Top Products</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-700 text-sm">Product A</span>
                        <span class="text-purple-600 font-semibold">42%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-purple-500 to-purple-600" style="width: 42%;"></div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <span class="text-slate-700 text-sm">Product B</span>
                        <span class="text-cyan-600 font-semibold">28%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-cyan-500 to-cyan-600" style="width: 28%;"></div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <span class="text-slate-700 text-sm">Product C</span>
                        <span class="text-green-600 font-semibold">30%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-green-500 to-green-600" style="width: 30%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl border bg-white shadow-md overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Recent Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-700">John Doe</td>
                            <td class="px-6 py-4 text-slate-900 font-semibold">$1,234.00</td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Completed</span></td>
                            <td class="px-6 py-4 text-slate-600">Dec 1, 2024</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-700">Jane Smith</td>
                            <td class="px-6 py-4 text-slate-900 font-semibold">$567.89</td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span></td>
                            <td class="px-6 py-4 text-slate-600">Dec 2, 2024</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-700">Mike Johnson</td>
                            <td class="px-6 py-4 text-slate-900 font-semibold">$2,345.67</td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Completed</span></td>
                            <td class="px-6 py-4 text-slate-600">Dec 2, 2024</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-700">Sarah Wilson</td>
                            <td class="px-6 py-4 text-slate-900 font-semibold">$890.12</td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Failed</span></td>
                            <td class="px-6 py-4 text-slate-600">Dec 3, 2024</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

</x-app-layout>
