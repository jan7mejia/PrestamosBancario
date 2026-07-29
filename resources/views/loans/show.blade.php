@extends('layouts.app')

@section('title', 'Detalle de Préstamo')

@push('styles')
<style>
    /* Modal animation */
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.92) translateY(20px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-box { animation: modalIn 0.25s ease forwards; }
</style>
@endpush

@section('content')

{{-- ===== HEADER ===== --}}
<div class="mb-8 flex flex-col lg:flex-row justify-between items-center lg:items-start bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40 print-hide gap-6">
    <div class="w-full lg:w-auto text-center lg:text-left">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Plan de Pagos</h1>
        <p class="text-sm text-gray-500 mt-1 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-1 sm:gap-2">
            <span>Cliente: <span class="font-bold text-teal-700 text-base">{{ $loan->client->name }}</span></span>
            @if($loan->status == 'paid')
                <span class="hidden sm:inline">·</span>
                <span class="inline-flex items-center space-x-1 bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-black border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>PRÉSTAMO CANCELADO</span>
                </span>
            @endif
        </p>
    </div>
    <div class="flex flex-col sm:flex-row items-center w-full lg:w-auto gap-3 sm:space-x-3 sm:gap-0">
        {{-- PDF del Plan Completo --}}
        <button id="downloadPdfBtn" class="w-full sm:w-auto justify-center flex items-center space-x-2 bg-white hover:bg-red-50 text-red-600 font-bold py-2.5 px-4 rounded-xl border border-red-200 shadow-sm transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
            <span>PDF Plan</span>
        </button>
        {{-- PDF de Pagos Realizados --}}
        <button id="downloadPaymentsPdfBtn" class="w-full sm:w-auto justify-center flex items-center space-x-2 bg-white hover:bg-emerald-50 text-emerald-700 font-bold py-2.5 px-4 rounded-xl border border-emerald-200 shadow-sm transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>PDF Pagos</span>
        </button>
        <a href="{{ route('loans.index') }}" class="w-full sm:w-auto justify-center flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-5 rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Volver</span>
        </a>
    </div>
</div>

{{-- ===== RESUMEN PROGRESO DE PAGO ===== --}}
@php
    $totalCuotas  = $loan->amortizations->count();
    $pagadas      = $loan->amortizations->where('status', 'paid')->count();
    $progreso     = $totalCuotas > 0 ? round(($pagadas / $totalCuotas) * 100) : 0;
    $totalPagado  = $loan->amortizations->where('status','paid')->sum('installment_amount');
    $totalPendiente = $loan->amortizations->where('status','pending')->sum('installment_amount');
@endphp

{{-- ===== KPI CARDS ===== --}}
<div id="pdf-content" class="bg-transparent p-0">
    {{-- PDF Header (hidden on screen) --}}
    <div class="hidden pdf-header mb-6 text-center border-b pb-4">
        <h2 class="text-2xl font-black text-teal-700">CrediTunari</h2>
        <p class="text-sm text-gray-500">Plan de Amortización — Documento Oficial</p>
        <p class="text-base font-bold mt-2">{{ $loan->client->name }} (CI: {{ $loan->client->ci }})</p>
        <p class="text-sm text-gray-500">Impreso el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-white/50 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-100 rounded-full opacity-50"></div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Capital Prestado</p>
        <p class="text-2xl font-black text-gray-900 relative z-10">Bs. {{ number_format($loan->amount, 2) }}</p>
    </div>
    <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-white/50 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-100 rounded-full opacity-50"></div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tasa / Plazo</p>
        <p class="text-2xl font-black text-gray-900 relative z-10">{{ $loan->interest_rate }}% <span class="text-base font-medium text-gray-500">/ {{ $loan->term_months }}m</span></p>
    </div>
    <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-white/50 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-100 rounded-full opacity-50"></div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Pagado</p>
        <p class="text-2xl font-black text-emerald-600 relative z-10">Bs. {{ number_format($totalPagado, 2) }}</p>
    </div>
    <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-5 rounded-3xl shadow-lg border border-teal-400 relative overflow-hidden text-white">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-white rounded-full opacity-10"></div>
        <p class="text-xs font-bold text-teal-100 uppercase tracking-wider mb-1">Progreso</p>
        <p class="text-2xl font-black relative z-10">{{ $pagadas }}/{{ $totalCuotas }} <span class="text-base font-medium">cuotas</span></p>
        <div class="mt-2 bg-white/20 rounded-full h-2 relative z-10">
            <div class="bg-white rounded-full h-2 transition-all duration-700" style="width: {{ $progreso }}%"></div>
        </div>
        <p class="text-xs text-teal-100 mt-1 relative z-10">{{ $progreso }}% completado</p>
    </div>
</div>

{{-- ===== TABLA DE CUOTAS CON PAGOS ===== --}}
<div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden print:shadow-none print:border-none print:bg-white">
    <div class="px-8 py-5 border-b border-gray-100 bg-slate-50/50 flex items-center justify-between print:bg-white">
        <h3 class="text-xl font-black text-gray-900 tracking-tight">Detalle de Cuotas</h3>
        <div class="flex items-center space-x-4 text-xs font-bold print:hidden">
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span><span class="text-gray-600">Pendiente</span></span>
            <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span><span class="text-gray-600">Pagada</span></span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-white">
                <tr>
                    <th scope="col" class="px-5 py-4 text-center font-bold text-slate-500 uppercase tracking-wider">Nº</th>
                    <th scope="col" class="px-5 py-4 text-center font-bold text-slate-500 uppercase tracking-wider">Vencimiento</th>
                    <th scope="col" class="px-5 py-4 text-right font-bold text-slate-500 uppercase tracking-wider">Cuota (Bs)</th>
                    <th scope="col" class="px-5 py-4 text-right font-bold text-slate-500 uppercase tracking-wider">Interés</th>
                    <th scope="col" class="px-5 py-4 text-right font-bold text-slate-500 uppercase tracking-wider">Capital</th>
                    <th scope="col" class="px-5 py-4 text-right font-bold text-slate-500 uppercase tracking-wider">Saldo</th>
                    <th scope="col" class="px-5 py-4 text-center font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="px-5 py-4 text-center font-bold text-slate-500 uppercase tracking-wider print:hidden">Fecha Pago</th>
                    <th scope="col" class="px-5 py-4 text-center font-bold text-slate-500 uppercase tracking-wider print:hidden">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-transparent">
                {{-- Fila 0: Desembolso --}}
                <tr class="bg-slate-50/50">
                    <td class="px-5 py-4 text-center font-bold text-gray-400">0</td>
                    <td class="px-5 py-4 text-center font-medium text-gray-500">{{ \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-right text-gray-400">—</td>
                    <td class="px-5 py-4 text-right text-gray-400">—</td>
                    <td class="px-5 py-4 text-right text-gray-400">—</td>
                    <td class="px-5 py-4 text-right font-black text-gray-900">Bs. {{ number_format($loan->amount, 2) }}</td>
                    <td class="px-5 py-4 text-center"><span class="text-xs font-bold bg-slate-200 text-slate-600 px-2.5 py-1 rounded-md">Desembolso</span></td>
                    <td class="px-5 py-4 print:hidden"></td>
                    <td class="px-5 py-4 print:hidden"></td>
                </tr>

                @foreach($loan->amortizations as $cuota)
                @php $isPaid = $cuota->status === 'paid'; @endphp
                <tr class="transition-colors {{ $isPaid ? 'bg-emerald-50/40' : 'hover:bg-amber-50/30' }}">
                    <td class="px-5 py-4 text-center font-bold {{ $isPaid ? 'text-emerald-700' : 'text-gray-600' }}">{{ $cuota->installment_number }}</td>
                    <td class="px-5 py-4 text-center font-medium text-gray-600">{{ \Carbon\Carbon::parse($cuota->due_date)->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-right font-black text-gray-900 text-base">{{ number_format($cuota->installment_amount, 2) }}</td>
                    <td class="px-5 py-4 text-right font-semibold text-red-500">{{ number_format($cuota->interest_amount, 2) }}</td>
                    <td class="px-5 py-4 text-right font-semibold text-emerald-600">{{ number_format($cuota->principal_amount, 2) }}</td>
                    <td class="px-5 py-4 text-right font-bold text-gray-700">{{ number_format($cuota->remaining_balance, 2) }}</td>

                    {{-- Estado --}}
                    <td class="px-5 py-4 text-center">
                        @if($isPaid)
                            <span class="px-2.5 py-1 inline-flex items-center space-x-1 text-xs font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <span>Pagada</span>
                            </span>
                        @else
                            <span class="px-2.5 py-1 inline-flex text-xs font-black rounded-full bg-amber-100 text-amber-800 border border-amber-200">Pendiente</span>
                        @endif
                    </td>

                    {{-- Fecha de pago real --}}
                    <td class="px-5 py-4 text-center text-xs text-gray-500 print:hidden">
                        @if($isPaid && $cuota->paid_at)
                            <span class="font-bold text-emerald-600">{{ $cuota->paid_at->format('d/m/Y') }}</span>
                            <br><span class="text-gray-400">{{ $cuota->paid_at->format('H:i') }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Botón de acción --}}
                    <td class="px-5 py-4 text-center print:hidden">
                        @if(!$isPaid)
                            <button
                                onclick="openPayModal({{ $cuota->id }}, {{ $cuota->installment_number }}, '{{ number_format($cuota->installment_amount, 2) }}', '{{ \Carbon\Carbon::parse($cuota->due_date)->format('d/m/Y') }}')"
                                class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white text-xs font-black px-3 py-1.5 rounded-xl shadow-md shadow-emerald-500/20 transition-all duration-300 transform hover:-translate-y-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Pagar</span>
                            </button>
                        @else
                            <button
                                onclick="openUnpayModal({{ $cuota->id }}, {{ $cuota->installment_number }})"
                                class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-600 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-200 hover:border-red-200 transition-all duration-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                <span>Anular</span>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-800 border-t-4 border-teal-500 text-white">
                <tr>
                    <td colspan="2" class="px-5 py-5 text-right font-black uppercase tracking-wider">Totales:</td>
                    <td class="px-5 py-5 text-right font-black text-lg text-blue-300">{{ number_format($loan->amortizations->sum('installment_amount'), 2) }}</td>
                    <td class="px-5 py-5 text-right font-bold text-red-300">{{ number_format($loan->amortizations->sum('interest_amount'), 2) }}</td>
                    <td class="px-5 py-5 text-right font-bold text-emerald-300">{{ number_format($loan->amortizations->sum('principal_amount'), 2) }}</td>
                    <td colspan="4" class="print:hidden"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</div>{{-- end pdf-content --}}

{{-- ===== PDF DE PAGOS REALIZADOS (contenido oculto) ===== --}}
<div id="pdf-payments-content" class="hidden bg-white p-6 rounded-xl">
    <div class="text-center border-b pb-4 mb-6">
        <h2 class="text-2xl font-black text-teal-700">CrediTunari</h2>
        <p class="text-sm text-gray-500">Reporte de Pagos Realizados</p>
        <p class="text-base font-bold mt-2">{{ $loan->client->name }} (CI: {{ $loan->client->ci }})</p>
        <p class="text-sm text-gray-500">Préstamo: Bs. {{ number_format($loan->amount, 2) }} · Sistema {{ ucfirst($loan->amortization_system) }} · {{ $loan->term_months }} meses</p>
        <p class="text-xs text-gray-400 mt-1">Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-teal-700 text-white">
                <th class="px-4 py-3 text-center">Nº Cuota</th>
                <th class="px-4 py-3 text-center">Vencimiento</th>
                <th class="px-4 py-3 text-right">Cuota (Bs)</th>
                <th class="px-4 py-3 text-right">Interés (Bs)</th>
                <th class="px-4 py-3 text-right">Capital (Bs)</th>
                <th class="px-4 py-3 text-center">Fecha de Pago</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loan->amortizations->where('status','paid') as $p)
            <tr class="border-b border-gray-200 {{ $loop->even ? 'bg-gray-50' : '' }}">
                <td class="px-4 py-3 text-center font-bold">{{ $p->installment_number }}</td>
                <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($p->due_date)->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-right font-bold">{{ number_format($p->installment_amount, 2) }}</td>
                <td class="px-4 py-3 text-right text-red-600">{{ number_format($p->interest_amount, 2) }}</td>
                <td class="px-4 py-3 text-right text-green-600">{{ number_format($p->principal_amount, 2) }}</td>
                <td class="px-4 py-3 text-center font-bold text-teal-700">{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-gray-400">No hay cuotas pagadas aún.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray-800 text-white font-black">
                <td colspan="2" class="px-4 py-3 text-right">TOTAL PAGADO:</td>
                <td class="px-4 py-3 text-right">{{ number_format($loan->amortizations->where('status','paid')->sum('installment_amount'), 2) }}</td>
                <td class="px-4 py-3 text-right text-red-300">{{ number_format($loan->amortizations->where('status','paid')->sum('interest_amount'), 2) }}</td>
                <td class="px-4 py-3 text-right text-green-300">{{ number_format($loan->amortizations->where('status','paid')->sum('principal_amount'), 2) }}</td>
                <td class="px-4 py-3 text-center">{{ $pagadas }}/{{ $totalCuotas }} cuotas</td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ===== MODAL DE CONFIRMAR PAGO ===== --}}
<div id="payModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePayModal()"></div>
    <div class="modal-box relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 border border-white/50">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900">Registrar Pago</h3>
            <p class="text-gray-500 text-sm mt-1">¿Confirmas el pago de la siguiente cuota?</p>
        </div>
        <div class="bg-slate-50 rounded-2xl p-5 mb-6 space-y-3">
            <div class="flex justify-between"><span class="text-sm text-gray-500">Nº de Cuota:</span><span class="text-sm font-black text-gray-900" id="modal-num">—</span></div>
            <div class="flex justify-between"><span class="text-sm text-gray-500">Monto a Pagar:</span><span class="text-base font-black text-emerald-600" id="modal-amount">—</span></div>
            <div class="flex justify-between"><span class="text-sm text-gray-500">Fecha de Vencimiento:</span><span class="text-sm font-bold text-gray-700" id="modal-date">—</span></div>
            <div class="flex justify-between border-t pt-3 mt-1"><span class="text-sm text-gray-500">Fecha de Pago:</span><span class="text-sm font-bold text-teal-600">Hoy, {{ now()->format('d/m/Y') }}</span></div>
        </div>
        <form id="payForm" method="POST" action="">
            @csrf
            <div class="flex space-x-3">
                <button type="button" onclick="closePayModal()" class="flex-1 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-black shadow-lg shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5">✅ Confirmar Pago</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL DE ANULAR PAGO ===== --}}
<div id="unpayModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeUnpayModal()"></div>
    <div class="modal-box relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 border border-white/50">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900">Anular Pago</h3>
            <p class="text-gray-500 text-sm mt-1">¿Estás seguro de anular el pago de la cuota <strong id="unpay-num" class="text-red-600">—</strong>?</p>
            <p class="text-xs text-red-500 mt-2 bg-red-50 rounded-xl px-3 py-2">Esta acción devolverá la cuota a estado "Pendiente".</p>
        </div>
        <form id="unpayForm" method="POST" action="">
            @csrf
            <div class="flex space-x-3">
                <button type="button" onclick="closeUnpayModal()" class="flex-1 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-red-500 hover:bg-red-600 text-white font-black shadow-lg shadow-red-500/30 transition-all duration-300">↩️ Anular Pago</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // ===== MODAL DE PAGO =====
    const baseUrl = '{{ url("/payments") }}';
    function openPayModal(id, num, amount, date) {
        document.getElementById('modal-num').textContent    = 'Cuota N\u00b0 ' + num;
        document.getElementById('modal-amount').textContent = 'Bs. ' + amount;
        document.getElementById('modal-date').textContent   = date;
        document.getElementById('payForm').action = baseUrl + '/' + id + '/pay';
        document.getElementById('payModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closePayModal() {
        document.getElementById('payModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // ===== MODAL ANULAR =====
    function openUnpayModal(id, num) {
        document.getElementById('unpay-num').textContent = num;
        document.getElementById('unpayForm').action = baseUrl + '/' + id + '/unpay';
        document.getElementById('unpayModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeUnpayModal() {
        document.getElementById('unpayModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // ===== PDF PLAN COMPLETO =====
    document.getElementById('downloadPdfBtn').addEventListener('click', function() {
        const headers = document.querySelectorAll('.pdf-header');
        headers.forEach(h => h.classList.remove('hidden'));
        const noPrint = document.querySelectorAll('.print-hide');
        noPrint.forEach(b => b.style.display = 'none');

        const element = document.getElementById('pdf-content');
        const opt = {
            margin: 8,
            filename: 'Plan_Pagos_{{ Str::slug($loan->client->name) }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save().then(() => {
            headers.forEach(h => h.classList.add('hidden'));
            noPrint.forEach(b => b.style.display = '');
        });
    });

    // ===== PDF PAGOS REALIZADOS =====
    document.getElementById('downloadPaymentsPdfBtn').addEventListener('click', function() {
        const el = document.getElementById('pdf-payments-content');
        el.classList.remove('hidden');
        const opt = {
            margin: 10,
            filename: 'Reporte_Pagos_{{ Str::slug($loan->client->name) }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(el).save().then(() => {
            el.classList.add('hidden');
        });
    });

    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closePayModal(); closeUnpayModal(); }
    });
</script>
@endpush
