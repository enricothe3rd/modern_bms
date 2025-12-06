<x-app-layout>
<main class="flex-1 overflow-auto bg-slate-50">
        <x-dashboard-header
            title="Dashboard"
            subtitle="Here's your overview."
        />

    <div class="p-8">
        <!-- System Overview Section -->
        <div class="mb-8 relative overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 rounded-3xl p-8 border border-white/20 shadow-xl backdrop-blur-sm">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-blue-400/20 to-indigo-400/20 rounded-full blur-xl animate-pulse"></div>
                <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-gradient-to-br from-purple-400/20 to-pink-400/20 rounded-full blur-xl animate-pulse delay-1000"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-gradient-to-br from-cyan-400/10 to-blue-400/10 rounded-full blur-2xl animate-pulse delay-500"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-indigo-900 to-purple-900 bg-clip-text text-transparent">System Overview</h3>
                            <p class="text-slate-600 mt-1">Real-time system statistics and metrics</p>
                        </div>
                    </div>
                    <a href="{{ route('management.index') }}" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl blur opacity-75 group-hover:opacity-100 transition-opacity"></div>
                        <svg class="relative w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="relative">Manage System</span>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    <!-- Total Items Card -->
                    <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 border border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform duration-300">{{ $systemStats['total_items'] }}</div>
                            <div class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Total Items</div>
                        </div>
                    </div>

                    <!-- Departments Card -->
                    <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 border border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform duration-300">{{ $systemStats['departments'] }}</div>
                            <div class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Departments</div>
                        </div>
                    </div>

                    <!-- Users Card -->
                    <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 border border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="text-4xl font-bold bg-gradient-to-r from-violet-600 to-purple-600 bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform duration-300">{{ $systemStats['users'] }}</div>
                            <div class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Users</div>
                        </div>
                    </div>

                    <!-- Roles Card -->
                    <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 border border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform duration-300">{{ $systemStats['roles'] }}</div>
                            <div class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Roles</div>
                        </div>
                    </div>

                    <!-- Forms Card -->
                    <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 border border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform duration-300">{{ $systemStats['forms'] }}</div>
                            <div class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Forms</div>
                        </div>
                    </div>

                    <!-- Accounts Card -->
                    <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 border border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform duration-300">{{ $systemStats['accounts'] }}</div>
                            <div class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">Accounts</div>
                        </div>
                    </div>
                </div>
            </div>
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
