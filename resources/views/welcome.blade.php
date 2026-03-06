<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU Budget Management System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --primary: rgb(47, 47, 49);
      --primary-light: rgb(240, 235, 250);
      --accent: rgb(100, 180, 220);
      --border: rgb(230, 235, 245);
      --foreground: rgb(20, 25, 50);
      --muted: rgb(110, 120, 140);
    }
  </style>
</head>
<body class="bg-white text-[var(--foreground)] antialiased">

  <nav class="border-b px-6 py-4 flex justify-between items-center sticky top-0 bg-white z-50" style="border-color: var(--border);">
    <div class="flex items-center gap-3">
      <img src="https://via.placeholder.com/40" alt="LGU Seal" class="w-10 h-10 rounded-full object-contain">
      <div>
        <span class="font-bold text-lg block leading-none">LGU ServiceHub</span>
        <span class="text-[10px] uppercase tracking-widest opacity-60 font-semibold">Budget Management</span>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <button class="px-5 py-2 text-sm font-medium border rounded-md transition hover:bg-gray-50" style="border-color: var(--border);">Log In</button>
      <button class="px-5 py-2 text-sm text-white rounded-md font-medium transition hover:opacity-90" style="background-color: var(--primary);">Register</button>
    </div>
  </nav>

  <header class="max-w-5xl mx-auto px-6 pt-12 pb-12">
    <div class="flex flex-col items-center text-center">
      <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=80&w=1200" alt="LGU Government Center" class="w-full h-80 object-cover rounded-3xl mb-10 shadow-lg border" style="border-color: var(--border);">

      <h1 class="text-4xl font-extrabold tracking-tight mb-4">LGU Financial & Budget Portal</h1>
      <p class="text-lg max-w-2xl" style="color: var(--muted);">Centralized management for departmental allocations, public fund tracking, and fiscal transparency.</p>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-6 pb-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      <a href="#" class="group p-8 rounded-2xl border bg-white hover:shadow-md transition-all" style="border-color: var(--border);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--primary-light);">
          <svg class="w-6 h-6" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-2 text-[var(--primary)]">Departmental Funds</h3>
        <p class="text-sm leading-relaxed" style="color: var(--muted);">Track budget utilization for Health, Education, and Security sectors.</p>
      </a>

      <a href="#" class="group p-8 rounded-2xl border bg-white hover:shadow-md transition-all" style="border-color: var(--border);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--primary-light);">
          <svg class="w-6 h-6" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-2 text-[var(--primary)]">Project Allocations</h3>
        <p class="text-sm leading-relaxed" style="color: var(--muted);">Monitor funding for public works, roads, and local infrastructure.</p>
      </a>

      <a href="#" class="group p-8 rounded-2xl border bg-white hover:shadow-md transition-all" style="border-color: var(--border);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--primary-light);">
          <svg class="w-6 h-6" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4 5H9m6 0h.01M12 12h.01M15 12h.01"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-2 text-[var(--primary)]">Social Services</h3>
        <p class="text-sm leading-relaxed" style="color: var(--muted);">Manage budget for community assistance and local programs.</p>
      </a>

      <a href="#" class="group p-8 rounded-2xl border bg-white hover:shadow-md transition-all" style="border-color: var(--border);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--primary-light);">
          <svg class="w-6 h-6" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-2 text-[var(--primary)]">Voucher Tracking</h3>
        <p class="text-sm leading-relaxed" style="color: var(--muted);">Real-time tracking of disbursement vouchers and payments.</p>
      </a>

      <a href="#" class="group p-8 rounded-2xl border bg-white hover:shadow-md transition-all" style="border-color: var(--border);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--primary-light);">
          <svg class="w-6 h-6" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-2 text-[var(--primary)]">COA Compliance</h3>
        <p class="text-sm leading-relaxed" style="color: var(--muted);">Ensuring all spending meets Commission on Audit standards.</p>
      </a>

      <a href="#" class="group p-8 rounded-2xl border bg-white hover:shadow-md transition-all" style="border-color: var(--border);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--primary-light);">
          <svg class="w-6 h-6" style="color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <h3 class="text-xl font-bold mb-2 text-[var(--primary)]">Bids & Awards</h3>
        <p class="text-sm leading-relaxed" style="color: var(--muted);">Manage financial aspects of procurement and bidding.</p>
      </a>

    </div>
  </main>

  <footer class="text-center py-12 border-t text-sm bg-gray-50" style="border-color: var(--border); color: var(--muted);">
    <p class="font-bold mb-1">OFFICE OF THE MUNICIPAL TREASURER</p>
    <p>&copy; 2026 Local Government Unit. Official Budget Portal.</p>
  </footer>

</body>
</html>
