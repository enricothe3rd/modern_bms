<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Figtree&family=Inter&family=Merriweather&family=Roboto&display=swap" rel="stylesheet">


    <!-- App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- DataTables CSS (local) -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons.dataTables.min.css') }}">
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-auto">
            {{ $slot }}
        </main>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('vendor/jquery/jquery-3.6.0.min.js') }}"></script>

    <!-- DataTables JS (local) -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.print.min.js') }}"></script>

    <!-- Required for Excel export -->
    <script src="{{ asset('vendor/jszip/jszip.min.js') }}"></script>

    <!-- Required for PDF export -->
    <script src="{{ asset('vendor/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/pdfmake/vfs_fonts.js') }}"></script>

    <!-- DataTable Initialization -->
    @stack('scripts')
</body>
</html>
