<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Keuangan Kampus')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>[x-cloak] { display: none !important; }</style>

    @stack('styles')
</head>
<body class="bg-gray-50">

    {{-- NAVBAR --}}
    @include('layouts.navigation')

    {{-- FLASH MESSAGE --}}
    {{-- @include('layouts.flash') --}}

    {{-- PAGE CONTENT --}}
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white shadow-lg mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © {{ date('Y') }} Sistem Keuangan Kampus. Flexible 7-Digit CoA System.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
