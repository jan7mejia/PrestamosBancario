@extends('layouts.app')

@section('title', 'Dashboard — CrediTunari')

@push('styles')
<style>
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-1 { animation: slideUp .45s ease both; }
    .anim-2 { animation: slideUp .45s .1s ease both; }
    .anim-3 { animation: slideUp .45s .2s ease both; }
    .anim-4 { animation: slideUp .45s .3s ease both; }
    .anim-5 { animation: slideUp .45s .4s ease both; }
    .anim-6 { animation: slideUp .45s .5s ease both; }

    @keyframes pulseGreen {
        0%,100% { opacity:1; }
        50%      { opacity:.35; }
    }
    .live-dot { animation: pulseGreen 2s infinite; }

    @keyframes growBar {
        from { width: 0; }
    }
    .grow-bar { animation: growBar 1.1s cubic-bezier(.4,0,.2,1) .6s both; }
</style>
@endpush

@section('content')

@php
    use App\Models\Loan;
    use App\Models\Client;
    use App\Models\Amortization;

    /* ── Clientes ── */
    $totalClientes    = Client::count();

    /* ── Préstamos ── */
    $prestamosActivos = Loan::where('status','active')->count();
    $prestamosPagados = Loan::where('status','paid')->count();
    $totalPrestamos   = Loan::count();
    $capitalActivo    = Loan::where('status','active')->sum('amount');
    $capitalTotal     = Loan::sum('amount');

    /* ── Cuotas ── */
    $totalCuotas      = Amortization::count();
    $cuotasPagadas    = Amortization::where('status','paid')->count();
    $cuotasPendientes = Amortization::where('status','pending')->count();
    $progresoCobro    = $totalCuotas > 0 ? round(($cuotasPagadas/$totalCuotas)*100) : 0;

    /* ── Montos ── */
    $totalRecaudado   = Amortization::where('status','paid')->sum('installment_amount');
    $totalPorCobrar   = Amortization::where('status','pending')->sum('installment_amount');
    $interesesCobrados= Amortization::where('status','paid')->sum('interest_amount');

    /* ── Próximas a vencer (7 días) ── */
    $proximasVencer = Amortization::with('loan.client')
        ->where('status','pending')
        ->whereBetween('due_date',[now(), now()->addDays(7)])
        ->orderBy('due_date')->take(5)->get();

    /* ── Préstamos recientes ── */
    $recentLoans = Loan::with(['client','amortizations'])->latest()->take(5)->get();
@endphp

{{-- ══════════════════════════════════════
     HEADER
══════════════════════════════════════ --}}
<div class="anim-1 mb-7 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard General</h1>
        <p class="text-gray-500 mt-1 flex items-center gap-2 text-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 live-dot inline-block flex-shrink-0"></span>
            Sistema en línea — Cartera de microcrédito · Cochabamba
        </p>
    </div>
    <div class="text-right hidden md:block">
        <p class="text-xs text-gray-400 uppercase tracking-widest">Actualizado</p>
        <p class="text-sm font-bold text-gray-700" id="live-clock">—</p>
    </div>
</div>

{{-- ══════════════════════════════════════
     FILA 1 — KPI PRINCIPALES (2×2 móvil, 4 en desktop)
══════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

    {{-- Clientes --}}
    <div class="anim-1 group bg-white/80 backdrop-blur-md rounded-3xl p-5 shadow-md border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden cursor-default">
        <div class="absolute -right-5 -top-5 w-24 h-24 bg-teal-100 rounded-full opacity-50 group-hover:opacity-80 transition-opacity"></div>
        <div class="w-11 h-11 bg-teal-100 text-teal-600 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-4xl font-black text-gray-900 leading-none">{{ $totalClientes }}</p>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-1">Clientes</p>
    </div>

    {{-- Préstamos activos --}}
    <div class="anim-2 group bg-white/80 backdrop-blur-md rounded-3xl p-5 shadow-md border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden cursor-default">
        <div class="absolute -right-5 -top-5 w-24 h-24 bg-blue-100 rounded-full opacity-50 group-hover:opacity-80 transition-opacity"></div>
        <div class="w-11 h-11 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-4xl font-black text-gray-900 leading-none">{{ $prestamosActivos }}</p>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-1">Préstamos Activos</p>
        @if($prestamosPagados > 0)
        <p class="text-xs text-emerald-600 font-bold mt-0.5">✓ {{ $prestamosPagados }} cancelado{{ $prestamosPagados > 1 ? 's' : '' }}</p>
        @endif
    </div>

    {{-- Capital activo --}}
    <div class="anim-3 group bg-white/80 backdrop-blur-md rounded-3xl p-5 shadow-md border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden cursor-default">
        <div class="absolute -right-5 -top-5 w-24 h-24 bg-amber-100 rounded-full opacity-50 group-hover:opacity-80 transition-opacity"></div>
        <div class="w-11 h-11 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xl font-black text-gray-900 leading-none">Bs. {{ number_format($capitalActivo, 0) }}</p>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-1">Capital Activo</p>
    </div>

    {{-- Total recaudado — tarjeta destacada --}}
    <div class="anim-4 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-3xl p-5 shadow-lg border border-teal-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden text-white cursor-default">
        <div class="absolute -right-5 -top-5 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="w-11 h-11 bg-white/20 rounded-2xl flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xl font-black leading-none">Bs. {{ number_format($totalRecaudado, 0) }}</p>
        <p class="text-xs font-bold text-teal-100 uppercase tracking-wider mt-1">Total Recaudado</p>
        <p class="text-xs text-teal-200 mt-0.5">Intereses: Bs. {{ number_format($interesesCobrados, 0) }}</p>
    </div>
</div>

{{-- ══════════════════════════════════════
     BARRA DE PROGRESO GLOBAL DE COBRO
══════════════════════════════════════ --}}
<div class="anim-2 bg-white/80 backdrop-blur-md rounded-3xl p-6 shadow-md border border-white/60 mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h2 class="text-base font-black text-gray-900">Progreso Global de Cobro</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $cuotasPagadas }} de {{ $totalCuotas }} cuotas cobradas en toda la cartera</p>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-center">
                <p class="text-xl font-black text-emerald-600">{{ $cuotasPagadas }}</p>
                <p class="text-xs font-bold text-gray-500">Cobradas</p>
            </div>
            <div class="text-center">
                <p class="text-xl font-black text-amber-500">{{ $cuotasPendientes }}</p>
                <p class="text-xs font-bold text-gray-500">Pendientes</p>
            </div>
            <div class="text-center">
                <p class="text-xl font-black text-teal-600">{{ $progresoCobro }}%</p>
                <p class="text-xs font-bold text-gray-500">Completado</p>
            </div>
        </div>
    </div>

    {{-- Barra principal --}}
    <div class="w-full bg-gray-100 rounded-full h-5 shadow-inner overflow-hidden">
        <div class="grow-bar h-5 rounded-full bg-gradient-to-r from-teal-400 to-emerald-500 flex items-center justify-end pr-3" style="width:{{ max($progresoCobro,2) }}%">
            @if($progresoCobro >= 8)
            <span class="text-white text-xs font-black">{{ $progresoCobro }}%</span>
            @endif
        </div>
    </div>

    <div class="flex justify-between mt-2 text-xs font-medium">
        <span class="text-emerald-600">Bs. {{ number_format($totalRecaudado, 2) }} cobrado</span>
        <span class="text-amber-500">Bs. {{ number_format($totalPorCobrar, 2) }} por cobrar</span>
    </div>
</div>

{{-- ══════════════════════════════════════
     FILA 2 — PRÉSTAMOS + PANEL LATERAL
══════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Tabla de préstamos recientes --}}
    <div class="anim-3 lg:col-span-2 bg-white/80 backdrop-blur-md rounded-3xl shadow-md border border-white/60 overflow-hidden">
        <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-gray-900">Préstamos Recientes</h2>
                <p class="text-xs text-gray-500 mt-0.5">Progreso de cobro por préstamo</p>
            </div>
            <a href="{{ route('loans.index') }}" class="text-xs font-bold text-teal-600 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-xl transition-colors">Ver todos →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentLoans as $loan)
            @php
                $lPag = $loan->amortizations->where('status','paid')->count();
                $lTot = $loan->amortizations->count();
                $lPct = $lTot > 0 ? round(($lPag/$lTot)*100) : 0;
                $lCobrado = $loan->amortizations->where('status','paid')->sum('installment_amount');
            @endphp
            <a href="{{ route('loans.show', $loan) }}" class="flex items-center justify-between px-7 py-4 hover:bg-slate-50/70 transition-colors group">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center text-slate-600 font-black text-sm shadow-inner flex-shrink-0">
                        {{ strtoupper(substr($loan->client->name,0,1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $loan->client->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ $loan->amortization_system }} · {{ $loan->term_months }}m · {{ $lPag }}/{{ $lTot }} cuotas</p>
                        <div class="mt-1.5 flex items-center gap-2">
                            <div class="w-24 bg-gray-100 rounded-full h-1.5 flex-shrink-0">
                                <div class="h-1.5 rounded-full bg-gradient-to-r from-teal-400 to-emerald-500" style="width:{{ $lPct }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-400">{{ $lPct }}%</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-black text-emerald-600">Bs. {{ number_format($loan->amount, 2) }}</p>
                        <p class="text-xs text-gray-400">Cobrado: {{ number_format($lCobrado,0) }}</p>
                    </div>
                    @if($loan->status=='active')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700 border border-blue-200 whitespace-nowrap">Activo</span>
                    @elseif($loan->status=='paid')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 whitespace-nowrap">✓ Cancelado</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-200 whitespace-nowrap">Mora</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="px-7 py-10 text-center">
                <p class="text-sm text-gray-400">No hay préstamos registrados aún.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Panel lateral --}}
    <div class="flex flex-col gap-4">

        {{-- Acciones rápidas --}}
        <div class="anim-4 bg-white/80 backdrop-blur-md rounded-3xl shadow-md border border-white/60 p-6">
            <h2 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Acciones Rápidas</h2>
            <div class="space-y-2.5">
                <a href="{{ route('clients.create') }}" class="flex items-center gap-3 bg-slate-50 hover:bg-teal-50 border border-slate-100 hover:border-teal-200 rounded-2xl p-3.5 transition-all duration-300 group">
                    <div class="w-9 h-9 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Nuevo Cliente</p><p class="text-xs text-gray-500">Registrar prestatario</p></div>
                </a>
                <a href="{{ route('loans.create') }}" class="flex items-center gap-3 bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 rounded-2xl p-3.5 transition-all duration-300 group">
                    <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Otorgar Préstamo</p><p class="text-xs text-gray-500">Generar plan de pagos</p></div>
                </a>
                <a href="{{ route('loans.index') }}" class="flex items-center gap-3 bg-slate-50 hover:bg-amber-50 border border-slate-100 hover:border-amber-200 rounded-2xl p-3.5 transition-all duration-300 group">
                    <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Cobrar Cuota</p><p class="text-xs text-gray-500">Registrar pago de cliente</p></div>
                </a>
            </div>
        </div>

        {{-- Resumen financiero --}}
        <div class="anim-5 bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-teal-500/10 rounded-full"></div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Resumen Financiero</p>
            <div class="space-y-3 relative z-10">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400">Capital prestado</span>
                    <span class="text-sm font-black text-white">Bs. {{ number_format($capitalTotal,2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400">Total recaudado</span>
                    <span class="text-sm font-black text-emerald-400">Bs. {{ number_format($totalRecaudado,2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400">Por cobrar</span>
                    <span class="text-sm font-black text-amber-400">Bs. {{ number_format($totalPorCobrar,2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400">Intereses cobrados</span>
                    <span class="text-sm font-black text-blue-400">Bs. {{ number_format($interesesCobrados,2) }}</span>
                </div>
                <div class="pt-3 border-t border-slate-700 flex justify-between items-center">
                    <span class="text-xs text-slate-400">Préstamos totales</span>
                    <span class="text-sm font-black text-white">{{ $totalPrestamos }} ({{ $prestamosActivos }} activos)</span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════
     FILA 3 — CUOTAS POR VENCER + CLIENTES
══════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    {{-- Cuotas próximas a vencer --}}
    <div class="anim-5 bg-white/80 backdrop-blur-md rounded-3xl shadow-md border border-white/60 overflow-hidden">
        <div class="px-6 py-4 border-b border-amber-100 bg-amber-50/60 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-gray-900">Cuotas por Vencer</h2>
                    <p class="text-xs text-gray-500">Próximos 7 días</p>
                </div>
            </div>
            @if($proximasVencer->count() > 0)
            <span class="bg-amber-500 text-white text-xs font-black px-2.5 py-1 rounded-full">
                {{ $proximasVencer->count() }}
            </span>
            @else
            <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                ✓ Todo al día
            </span>
            @endif
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($proximasVencer as $cuota)
            @php
                $dias = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($cuota->due_date)->startOfDay(), false);
            @endphp
            <a href="{{ route('loans.show', $cuota->loan) }}" class="flex items-center justify-between px-6 py-3.5 hover:bg-amber-50/40 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0">
                        {{ strtoupper(substr($cuota->loan->client->name,0,1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $cuota->loan->client->name }}</p>
                        <p class="text-xs text-gray-500">Cuota N° {{ $cuota->installment_number }} · Vence {{ \Carbon\Carbon::parse($cuota->due_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-2">
                    <p class="text-sm font-black text-amber-600">Bs. {{ number_format($cuota->installment_amount,2) }}</p>
                    <span class="text-xs font-bold px-2 py-1 rounded-lg {{ $dias <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $dias <= 0 ? 'Hoy' : 'En '.$dias.'d' }}
                    </span>
                </div>
            </a>
            @empty
            <div class="px-6 py-10 text-center">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-sm text-gray-400 font-medium">No hay cuotas por vencer esta semana.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Galería de clientes --}}
    <div class="anim-6 bg-white/80 backdrop-blur-md rounded-3xl shadow-md border border-white/60 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-sm font-black text-gray-900">Clientes en Cartera</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ $totalClientes }} prestatario{{ $totalClientes!=1?'s':'' }} registrado{{ $totalClientes!=1?'s':'' }}</p>
            </div>
            <a href="{{ route('clients.create') }}" class="flex items-center gap-1.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-md shadow-emerald-500/20 transition-all duration-300 hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Agregar
            </a>
        </div>
        <div class="grid grid-cols-4 gap-3">
            @forelse(Client::latest()->take(8)->get() as $client)
            <a href="{{ route('clients.show', $client) }}" class="flex flex-col items-center p-3 bg-slate-50 hover:bg-teal-50 rounded-2xl border border-slate-100 hover:border-teal-200 transition-all duration-300 group">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-400 to-emerald-500 rounded-full flex items-center justify-center text-white font-black text-xl shadow-md shadow-teal-500/20 mb-2 group-hover:scale-110 transition-transform">
                    {{ strtoupper(substr($client->name,0,1)) }}
                </div>
                <p class="text-xs font-bold text-gray-800 text-center leading-tight truncate w-full text-center">{{ Str::limit($client->name,10) }}</p>
            </a>
            @empty
            <div class="col-span-4 py-8 text-center text-sm text-gray-400">No hay clientes registrados.</div>
            @endforelse

            @if(Client::count() > 8)
            <a href="{{ route('clients.index') }}" class="flex flex-col items-center justify-center p-3 bg-slate-100 hover:bg-teal-50 rounded-2xl border border-slate-200 hover:border-teal-200 transition-all duration-300 group">
                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center text-slate-500 font-black text-sm mb-2 group-hover:bg-teal-100 group-hover:text-teal-600 transition-colors">
                    +{{ Client::count()-8 }}
                </div>
                <p class="text-xs font-bold text-gray-400">más</p>
            </a>
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    (function updateClock(){
        const el = document.getElementById('live-clock');
        if(el){
            const now = new Date();
            el.textContent = now.toLocaleDateString('es-BO',{weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});
        }
        setTimeout(updateClock, 60000);
    })();
</script>
@endpush
