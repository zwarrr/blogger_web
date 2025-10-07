<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
    @stack('head')
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 font-sans antialiased">
    <!-- Sidebar -->
    @if(auth()->user()->role === 'admin')
        @include('admin.sidebar')
    @elseif(auth()->user()->role === 'auditor')
        @include('auditor.sidebar')
    @elseif(auth()->user()->role === 'author')
        @include('author.sidebar')
    @endif
    
    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>