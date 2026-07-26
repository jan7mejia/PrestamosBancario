@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@push('styles')
<style>
    /* Full screen mountain background */
    .login-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-image: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop'); /* Mountain landscape */
        background-size: cover;
        background-position: center;
        z-index: -2;
        animation: subtleZoom 20s infinite alternate ease-in-out;
    }
    .login-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: linear-gradient(to bottom, rgba(15, 23, 42, 0.2), rgba(15, 23, 42, 0.6));
        z-index: -1;
    }

    /* Animations */
    @keyframes subtleZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.05); }
    }
    @keyframes slideUpFade {
        0% { opacity: 0; transform: translateY(40px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideRightFade {
        0% { opacity: 0; transform: translateX(-30px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideLeftFade {
        0% { opacity: 0; transform: translateX(30px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }

    .animate-container { animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-left { opacity: 0; animation: slideRightFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards; }
    .animate-right { opacity: 0; animation: slideLeftFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.5s forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }

    /* Input Focus Glow */
    .input-glow:focus-within {
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="login-bg"></div>
<div class="login-overlay"></div>

<div class="min-h-screen flex items-center justify-center p-4">
    
    <!-- Split Card Container -->
    <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-2xl overflow-hidden shadow-2xl border border-white/20 relative z-10 animate-container">
        
        <!-- Left Side: Login Form -->
        <div class="w-full md:w-1/2 bg-white/40 backdrop-blur-md p-8 sm:p-12 flex flex-col justify-center animate-left">
            
            <div class="mb-8 flex items-center space-x-2 text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-3xl font-bold">Iniciar Sesión</h2>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-1">Usuario / Correo</label>
                    <div class="relative input-glow transition-all duration-300">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="block w-full pl-10 pr-3 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-medium bg-white/80 inline-block px-2 rounded">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-800 mb-1">Contraseña</label>
                    <div class="relative input-glow transition-all duration-300">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="block w-full pl-10 pr-3 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: Info Panel -->
        <div class="w-full md:w-1/2 bg-slate-900/90 backdrop-blur-xl p-8 sm:p-12 text-white flex flex-col justify-center animate-right">
            
            <div class="flex items-center space-x-3 mb-6">
                <!-- Mountain Logo -->
                <svg class="w-10 h-10 text-blue-400 animate-float" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 6l-4.22 5.63 1.22 1.63L14 9.33l6 8H4l6-8 1.88 2.5 1.23-1.63L10 6 2 17.33h20L14 6z"/>
                </svg>
                <h2 class="text-3xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-300">CrediTunari</h2>
            </div>

            <div class="space-y-4">
                <p class="text-sm text-gray-300 leading-relaxed">
                    Sistema centralizado en <span class="font-bold text-white">Cochabamba, Bolivia</span>, diseñado para gestionar de manera eficiente carteras de microcrédito.
                </p>
                
                <ul class="space-y-3 mt-6 text-sm text-gray-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Planes de pago flexibles (Francés, Alemán, Americano)
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Geolocalización precisa de clientes
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Control total de amortizaciones y reportes
                    </li>
                </ul>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <p class="text-xs text-gray-400 italic">
                        "Facilitando el crecimiento financiero con tecnología de punta."
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
