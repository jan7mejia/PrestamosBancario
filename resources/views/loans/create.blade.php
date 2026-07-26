@extends('layouts.app')

@section('title', 'Nuevo Préstamo')

@section('content')
<div class="mb-8 flex justify-between items-center bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Otorgar Nuevo Préstamo</h1>
        <p class="text-sm text-gray-500 mt-1">Configura las condiciones financieras y genera el plan de pagos.</p>
    </div>
    <a href="{{ route('loans.index') }}" class="flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-5 rounded-xl transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        <span>Volver a la Lista</span>
    </a>
</div>

<div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden">
    <form action="{{ route('loans.store') }}" method="POST" class="p-8 sm:p-10">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            <div class="space-y-7 lg:col-span-2">
                <div>
                    <label for="client_id" class="block text-sm font-bold text-gray-700 mb-1.5">Seleccionar Cliente Prestatario</label>
                    <select id="client_id" name="client_id" required class="block w-full bg-white border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                        <option value="">Seleccione un cliente de la cartera...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} - CI: {{ $client->ci }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-7 bg-slate-50/50 p-6 rounded-2xl border border-gray-100">
                <h3 class="text-sm font-black text-teal-700 uppercase tracking-wider mb-4 border-b border-teal-100 pb-2">Condiciones Financieras</h3>
                <div>
                    <label for="amount" class="block text-sm font-bold text-gray-700 mb-1.5">Capital a Prestar (Bs.)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">Bs.</span>
                        </div>
                        <input type="number" step="0.01" name="amount" id="amount" required value="{{ old('amount') }}" class="block w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm text-gray-900 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                    </div>
                </div>
                
                <div>
                    <label for="interest_rate" class="block text-sm font-bold text-gray-700 mb-1.5">Tasa de Interés Anual (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="interest_rate" id="interest_rate" required value="{{ old('interest_rate') }}" placeholder="Ej: 12.5" class="block w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl shadow-sm text-gray-900 font-bold focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-7 bg-slate-50/50 p-6 rounded-2xl border border-gray-100">
                <h3 class="text-sm font-black text-teal-700 uppercase tracking-wider mb-4 border-b border-teal-100 pb-2">Plazos y Fechas</h3>
                <div>
                    <label for="term_months" class="block text-sm font-bold text-gray-700 mb-1.5">Plazo Total (Meses)</label>
                    <div class="relative">
                        <input type="number" name="term_months" id="term_months" required value="{{ old('term_months') }}" class="block w-full pl-4 pr-16 py-3 bg-white border border-gray-200 rounded-xl shadow-sm text-gray-900 font-bold focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">meses</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-bold text-gray-700 mb-1.5">Fecha de Desembolso / Inicio</label>
                    <input type="date" name="start_date" id="start_date" required value="{{ old('start_date', date('Y-m-d')) }}" class="block w-full bg-white border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 font-bold focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                </div>
            </div>

            <div class="lg:col-span-2 mt-4">
                <label class="block text-sm font-black text-gray-900 mb-4 text-center text-lg">Seleccionar Sistema de Amortización</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="amortization-options">
                    
                    <!-- Francés -->
                    <label class="relative flex cursor-pointer rounded-2xl border-2 bg-white p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-teal-500 ring-4 ring-teal-500/20">
                        <input type="radio" name="amortization_system" value="frances" class="sr-only" checked>
                        <div class="flex flex-1 flex-col justify-center items-center text-center">
                            <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <span class="block text-lg font-black text-gray-900 mb-1">Sistema Francés</span>
                            <span class="block text-xs font-medium text-gray-500">Cuota total siempre fija. Interés decreciente y capital creciente. (Recomendado)</span>
                        </div>
                    </label>

                    <!-- Alemán -->
                    <label class="relative flex cursor-pointer rounded-2xl border-2 bg-white p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-gray-200 hover:border-teal-300">
                        <input type="radio" name="amortization_system" value="aleman" class="sr-only">
                        <div class="flex flex-1 flex-col justify-center items-center text-center">
                            <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center mb-3 transition-colors duration-300 icon-bg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <span class="block text-lg font-black text-gray-900 mb-1">Sistema Alemán</span>
                            <span class="block text-xs font-medium text-gray-500">Amortización de capital fija. La cuota total va disminuyendo cada mes.</span>
                        </div>
                    </label>

                    <!-- Americano -->
                    <label class="relative flex cursor-pointer rounded-2xl border-2 bg-white p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border-gray-200 hover:border-teal-300">
                        <input type="radio" name="amortization_system" value="americano" class="sr-only">
                        <div class="flex flex-1 flex-col justify-center items-center text-center">
                            <div class="w-12 h-12 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center mb-3 transition-colors duration-300 icon-bg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="block text-lg font-black text-gray-900 mb-1">Sistema Americano</span>
                            <span class="block text-xs font-medium text-gray-500">Solo se pagan intereses mensuales. Todo el capital se devuelve al final.</span>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        <div class="mt-12 flex justify-end items-center bg-gray-50/80 -mx-10 -mb-10 p-6 sm:px-10 border-t border-gray-200">
            <button type="submit" class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white py-3 px-8 rounded-xl shadow-lg shadow-emerald-500/40 text-base font-black tracking-wide transition-all duration-300 transform hover:-translate-y-0.5 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Generar Préstamo y Plan de Pagos</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = document.querySelectorAll('#amortization-options label');
        const radios = document.querySelectorAll('#amortization-options input[type="radio"]');

        function updateRadioStyles() {
            labels.forEach(label => {
                const radio = label.querySelector('input[type="radio"]');
                const iconBg = label.querySelector('.icon-bg');
                
                if (radio.checked) {
                    label.classList.remove('border-gray-200', 'hover:border-teal-300');
                    label.classList.add('border-teal-500', 'ring-4', 'ring-teal-500/20');
                    if(iconBg) {
                        iconBg.classList.remove('bg-gray-100', 'text-gray-500');
                        iconBg.classList.add('bg-teal-100', 'text-teal-600');
                    }
                } else {
                    label.classList.add('border-gray-200', 'hover:border-teal-300');
                    label.classList.remove('border-teal-500', 'ring-4', 'ring-teal-500/20');
                    if(iconBg) {
                        iconBg.classList.add('bg-gray-100', 'text-gray-500');
                        iconBg.classList.remove('bg-teal-100', 'text-teal-600');
                    }
                }
            });
        }

        radios.forEach(radio => {
            radio.addEventListener('change', updateRadioStyles);
        });
    });
</script>
@endpush
