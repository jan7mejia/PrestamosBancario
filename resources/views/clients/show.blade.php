@extends('layouts.app')

@section('title', 'Perfil del Cliente')

@section('content')

@php
    $client->load('loans.amortizations');
    $totalPrestamos  = $client->loans->count();
    $prestamosActivos = $client->loans->where('status','active')->count();
    $prestamosPagados = $client->loans->where('status','paid')->count();
    $capitalTotal    = $client->loans->sum('amount');
    $totalPagado     = $client->loans->flatMap->amortizations->where('status','paid')->sum('installment_amount');
@endphp

{{-- ===== HEADER ===== --}}
<div class="mb-8 flex flex-col md:flex-row justify-between items-center md:items-start lg:items-center bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40 gap-6">
    <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-4 sm:space-y-0 sm:space-x-5 w-full md:w-auto">
        {{-- Avatar Grande --}}
        <div class="w-20 h-20 bg-gradient-to-br from-teal-400 to-emerald-600 rounded-3xl flex items-center justify-center text-white font-black text-4xl shadow-xl shadow-teal-500/30 flex-shrink-0 mx-auto sm:mx-0">
            {{ strtoupper(substr($client->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ $client->name }}</h1>
            <div class="mt-2 flex flex-wrap justify-center sm:justify-start gap-2">
                <span class="inline-flex items-center space-x-1 bg-slate-100 px-2.5 py-1 rounded-md text-xs font-bold text-slate-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    <span>CI: {{ $client->ci }}</span>
                </span>
                @if($client->phone)
                <span class="inline-flex items-center space-x-1 bg-slate-100 px-2.5 py-1 rounded-md text-xs font-bold text-slate-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>{{ $client->phone }}</span>
                </span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row items-center w-full md:w-auto gap-3 sm:gap-0 sm:space-x-3">
        <a href="{{ route('clients.edit', $client) }}" class="w-full sm:w-auto justify-center flex items-center space-x-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            <span>Editar</span>
        </a>
        <a href="{{ route('clients.index') }}" class="w-full sm:w-auto justify-center flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-5 rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Volver</span>
        </a>
    </div>
</div>

{{-- ===== KPI CARDS DEL CLIENTE ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-white/50 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-teal-100 rounded-full opacity-50"></div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Préstamos</p>
        <p class="text-4xl font-black text-gray-900 relative z-10">{{ $totalPrestamos }}</p>
    </div>
    <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-white/50 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-100 rounded-full opacity-50"></div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Activos / Pagados</p>
        <p class="text-4xl font-black text-gray-900 relative z-10">{{ $prestamosActivos }}<span class="text-lg font-medium text-gray-400">/{{ $prestamosPagados }}</span></p>
    </div>
    <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-white/50 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-100 rounded-full opacity-50"></div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Capital Recibido</p>
        <p class="text-2xl font-black text-gray-900 relative z-10">Bs. {{ number_format($capitalTotal, 0) }}</p>
    </div>
    <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-5 rounded-3xl shadow-lg border border-teal-400 relative overflow-hidden text-white">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-white rounded-full opacity-10"></div>
        <p class="text-xs font-bold text-teal-100 uppercase tracking-wider mb-2">Total Pagado</p>
        <p class="text-2xl font-black relative z-10">Bs. {{ number_format($totalPagado, 0) }}</p>
    </div>
</div>

{{-- ===== GRID: INFO + MAPA ===== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Info Personal --}}
    <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-lg border border-white/50 p-7">
        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-5 pb-3 border-b border-gray-100 flex items-center space-x-2">
            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span>Información Personal</span>
        </h3>
        <dl class="space-y-5">
            <div class="bg-slate-50 rounded-2xl p-4">
                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Carnet de Identidad</dt>
                <dd class="text-lg font-black text-gray-900">{{ $client->ci }}</dd>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4">
                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Teléfono / Celular</dt>
                <dd class="text-base font-bold text-gray-900">{{ $client->phone ?? 'No registrado' }}</dd>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4">
                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dirección</dt>
                <dd class="text-sm font-medium text-gray-700 leading-relaxed">{{ $client->address ?? 'No registrada' }}</dd>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4">
                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Registrado el</dt>
                <dd class="text-sm font-bold text-gray-700">{{ $client->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Mapa --}}
    <div class="md:col-span-2 bg-white/80 backdrop-blur-md rounded-3xl shadow-lg border border-white/50 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/50 border-b border-gray-100 flex items-center space-x-2">
            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Ubicación del Cliente</h3>
        </div>
        @if($client->latitude && $client->longitude)
            <div id="map" class="w-full h-72"></div>
        @else
            <div class="h-72 flex flex-col items-center justify-center text-gray-400">
                <svg class="w-14 h-14 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                <p class="text-sm font-medium">Ubicación no registrada en el mapa.</p>
                <a href="{{ route('clients.edit', $client) }}" class="mt-2 text-xs font-bold text-teal-600 hover:underline">Editar para agregar ubicación →</a>
            </div>
        @endif
    </div>
</div>

{{-- ===== HISTORIAL DE PRÉSTAMOS DEL CLIENTE ===== --}}
<div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-lg border border-white/50 overflow-hidden">
    <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <div>
                <h2 class="text-lg font-black text-gray-900">Historial de Préstamos</h2>
                <p class="text-xs text-gray-500">{{ $totalPrestamos }} préstamo{{ $totalPrestamos != 1 ? 's' : '' }} registrado{{ $totalPrestamos != 1 ? 's' : '' }}</p>
            </div>
        </div>
        <a href="{{ route('loans.create') }}" class="flex items-center space-x-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-md shadow-emerald-500/20 transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Nuevo Préstamo</span>
        </a>
    </div>

    @forelse($client->loans as $loan)
    @php
        $lPagadas  = $loan->amortizations->where('status','paid')->count();
        $lTotal    = $loan->amortizations->count();
        $lProgreso = $lTotal > 0 ? round(($lPagadas / $lTotal) * 100) : 0;
        $lPagadoMonto = $loan->amortizations->where('status','paid')->sum('installment_amount');
    @endphp
    <div class="px-7 py-5 border-b border-gray-50 last:border-b-0 hover:bg-slate-50/50 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            {{-- Info préstamo --}}
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-base font-black text-emerald-600">Bs. {{ number_format($loan->amount, 2) }}</span>
                        <span class="text-xs text-gray-400">·</span>
                        <span class="text-sm font-bold text-gray-700 capitalize">Sistema {{ $loan->amortization_system }}</span>
                        <span class="text-xs text-gray-400">·</span>
                        <span class="text-sm text-gray-500">{{ $loan->term_months }} meses al {{ $loan->interest_rate }}%</span>
                    </div>
                    <p class="text-xs text-gray-400">Inicio: {{ \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Estado y acciones --}}
            <div class="flex items-center space-x-4 sm:flex-shrink-0">
                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-0.5">Pagado: <span class="font-bold text-emerald-600">Bs. {{ number_format($lPagadoMonto, 0) }}</span></p>
                    <div class="flex items-center space-x-2">
                        <div class="w-28 bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full bg-gradient-to-r from-teal-400 to-emerald-500" style="width: {{ $lProgreso }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-500">{{ $lPagadas }}/{{ $lTotal }}</span>
                    </div>
                </div>

                @if($loan->status == 'active')
                    <span class="px-3 py-1.5 text-xs font-black rounded-full bg-blue-100 text-blue-700 border border-blue-200 whitespace-nowrap">Activo</span>
                @elseif($loan->status == 'paid')
                    <span class="px-3 py-1.5 text-xs font-black rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 whitespace-nowrap flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        <span>Cancelado</span>
                    </span>
                @else
                    <span class="px-3 py-1.5 text-xs font-black rounded-full bg-red-100 text-red-700 border border-red-200 whitespace-nowrap">Mora</span>
                @endif

                <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center px-3 py-1.5 border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl text-xs font-bold transition-colors">
                    Ver Plan →
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="px-7 py-12 text-center">
        <svg class="w-14 h-14 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <p class="text-sm text-gray-400 font-medium">Este cliente aún no tiene préstamos registrados.</p>
        <a href="{{ route('loans.create') }}" class="mt-3 inline-block text-sm font-bold text-teal-600 hover:underline">Otorgar primer préstamo →</a>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
@if($client->latitude && $client->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([{{ $client->latitude }}, {{ $client->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        L.marker([{{ $client->latitude }}, {{ $client->longitude }}])
            .addTo(map)
            .bindPopup("<b>{{ $client->name }}</b><br>{{ $client->address ?? '' }}")
            .openPopup();
    });
</script>
@endif
@endpush
