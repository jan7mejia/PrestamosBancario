@extends('layouts.app')

@section('title', 'Lista de Préstamos')

@section('content')
<div class="mb-8 flex justify-between items-center bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Gestión de Préstamos</h1>
        <p class="text-sm text-gray-500 mt-1">Control general de la cartera de créditos y estado de amortizaciones.</p>
    </div>
    <a href="{{ route('loans.create') }}" class="flex items-center space-x-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        <span>Nuevo Préstamo</span>
    </a>
</div>

<div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-slate-50/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Cliente</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Monto</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Sistema</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Plazo</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-transparent">
                @forelse($loans as $loan)
                <tr class="hover:bg-teal-50/50 transition-colors group">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 font-bold shadow-inner border border-slate-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $loan->client->name }}</div>
                                <div class="text-xs text-gray-500">CI: {{ $loan->client->ci }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm text-emerald-600 font-black">Bs. {{ number_format($loan->amount, 2) }}</div>
                        <div class="text-xs text-gray-500">{{ $loan->interest_rate }}% anual</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="inline-flex items-center space-x-1 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-medium border border-blue-100 capitalize">
                            {{ $loan->amortization_system }}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 font-medium">
                        {{ $loan->term_months }} meses
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        @if($loan->status == 'active')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">Activo</span>
                        @elseif($loan->status == 'paid')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-slate-100 text-slate-800 border border-slate-200 shadow-sm">Pagado</span>
                        @else
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200 shadow-sm">Mora</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center px-3 py-1.5 border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100 hover:border-teal-300 rounded-lg transition-colors">
                            Ver Plan de Pagos
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 whitespace-nowrap text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium">No hay préstamos registrados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
