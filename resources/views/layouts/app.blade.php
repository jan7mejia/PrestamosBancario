<!DOCTYPE html>
<html lang="es" class="bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrediTunari | @yield('title')</title>
    <!-- Tailwind CSS (CDN for immediate results without build step) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0d9488', // teal-600
                        secondary: '#059669', // emerald-600
                        accent: '#f59e0b',
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Alpine.js for Mobile Menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased text-gray-800 flex flex-col min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50">
    
    @auth
    <!-- Glassmorphic Navbar -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-200/50 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side: Logo & Desktop Links -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center space-x-2 group">
                        <!-- Logo de Montaña (Tunari) -->
                        <div class="bg-primary/10 p-2 rounded-xl group-hover:bg-primary/20 transition-colors">
                            <svg class="w-6 h-6 text-primary transform group-hover:-translate-y-0.5 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 6l-4.22 5.63 1.22 1.63L14 9.33l6 8H4l6-8 1.88 2.5 1.23-1.63L10 6 2 17.33h20L14 6z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary tracking-tight">CrediTunari</span>
                    </a>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden sm:-my-px sm:ml-10 sm:flex sm:space-x-4">
                        <a href="{{ route('dashboard') }}" class="{{ Request::is('dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <a href="{{ route('clients.index') }}" class="{{ Request::is('clients*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Clientes
                        </a>
                        <a href="{{ route('loans.index') }}" class="{{ Request::is('loans*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Préstamos
                        </a>
                    </div>
                </div>

                <!-- Right side: Logout (Desktop) & Hamburger (Mobile) -->
                <div class="flex items-center">
                    <!-- Desktop Logout -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-1 text-sm font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span>Salir</span>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary" aria-expanded="false">
                            <span class="sr-only">Abrir menú principal</span>
                            <!-- Icon when menu is closed -->
                            <svg x-show="!mobileMenuOpen" class="block w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Icon when menu is open -->
                            <svg x-show="mobileMenuOpen" style="display: none;" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu, show/hide based on menu state. -->
        <div x-show="mobileMenuOpen" x-transition.opacity style="display: none;" class="sm:hidden border-t border-gray-200 bg-white/95 backdrop-blur-md">
            <div class="pt-2 pb-3 space-y-1 px-4">
                <a href="{{ route('dashboard') }}" class="{{ Request::is('dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                <a href="{{ route('clients.index') }}" class="{{ Request::is('clients*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Clientes</a>
                <a href="{{ route('loans.index') }}" class="{{ Request::is('loans*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Préstamos</a>
            </div>
            <div class="pt-4 pb-4 border-t border-gray-200 px-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 w-full text-left text-red-600 hover:bg-red-50 px-3 py-2 rounded-md text-base font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Changed justify-center to just block display for internal pages so they flow naturally -->
    <main class="flex-grow w-full max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 fade-in z-10">
        @yield('content')
    </main>

    @if(!Request::is('login'))
    <footer class="bg-white/80 backdrop-blur-md border-t border-gray-200 mt-auto shadow-inner relative z-10">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500 font-medium">&copy; {{ date('Y') }} CrediTunari - Cochabamba. Sistema de Gestión Financiera Premium.</p>
        </div>
    </footer>
    @endif

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- SweetAlert2 Logic -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Ups!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#1e3a8a'
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
