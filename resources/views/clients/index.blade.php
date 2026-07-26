@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="mb-8 flex justify-between items-center bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Directorio de Clientes</h1>
        <p class="text-sm text-gray-500 mt-1">Gestiona los prestatarios y su ubicación geográfica.</p>
    </div>
    <a href="{{ route('clients.create') }}" class="flex items-center space-x-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        <span>Nuevo Cliente</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Tabla de Clientes -->
    <div class="lg:col-span-2 bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre del Cliente</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">CI</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Contacto</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-transparent">
                    @forelse($clients as $client)
                    <tr class="hover:bg-teal-50/50 transition-colors cursor-pointer group" onclick="focusMap({{ $client->latitude ?? 'null' }}, {{ $client->longitude ?? 'null' }}, '{{ $client->name }}')">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full flex items-center justify-center text-teal-600 font-bold shadow-inner">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $client->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($client->address, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-600">{{ $client->ci }}</td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center space-x-1 bg-slate-100 px-2.5 py-1 rounded-md text-xs font-medium text-slate-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span>{{ $client->phone ?? 'N/A' }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center px-3 py-1.5 border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100 hover:border-teal-300 rounded-lg transition-colors">
                                Ver Perfil
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 whitespace-nowrap text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p class="text-sm font-medium">No hay clientes registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mapa de Clientes -->
    <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden flex flex-col h-[600px]">
        <div class="px-6 py-5 bg-slate-50/50 border-b border-gray-100 flex items-center space-x-2">
            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Mapa de Ubicaciones</h3>
        </div>
        <div id="map" class="flex-grow z-0"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar mapa centrado en Cochabamba, Bolivia
        var map = L.map('map').setView([-17.3895, -66.1568], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var markers = [];

        @foreach($clients as $client)
            @if($client->latitude && $client->longitude)
                var marker = L.marker([{{ $client->latitude }}, {{ $client->longitude }}])
                    .bindPopup("<b>{{ $client->name }}</b><br>{{ $client->phone }}")
                    .addTo(map);
                markers.push({
                    lat: {{ $client->latitude }},
                    lng: {{ $client->longitude }},
                    marker: marker
                });
            @endif
        @endforeach

        // Función para enfocar un cliente al hacer clic en la tabla
        window.focusMap = function(lat, lng, name) {
            if(lat && lng) {
                map.setView([lat, lng], 16);
            }
        }
    });
</script>
@endpush
