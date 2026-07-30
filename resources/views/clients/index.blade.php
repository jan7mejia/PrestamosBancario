@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="mb-8 flex flex-col lg:flex-row justify-between items-center bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40 gap-5">
    <div class="w-full lg:w-auto text-center lg:text-left">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Directorio de Clientes</h1>
        <p class="text-sm text-gray-500 mt-1 hidden md:block">Gestiona los prestatarios y su ubicación geográfica.</p>
    </div>
    
    <!-- Buscador Integrado Responsivo -->
    <div class="w-full lg:w-1/3 relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input type="text" id="searchClients" placeholder="Buscar por nombre, CI o teléfono..."
               class="block w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300 shadow-inner"
               oninput="filterClients()">
        <button id="clearSearchClients" onclick="clearClientSearch()" class="hidden absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <p id="searchResultCount" class="absolute -bottom-6 left-2 text-xs text-teal-600 font-bold hidden"></p>
    </div>

    <div class="w-full lg:w-auto">
        <a href="{{ route('clients.create') }}" class="w-full lg:w-auto flex justify-center items-center space-x-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Nuevo Cliente</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Tabla de Clientes -->
    <div class="lg:col-span-2 bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100" id="clientsTable">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre del Cliente</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">CI</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Contacto</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-transparent" id="clientsBody">
                    @forelse($clients as $client)
                    <tr class="client-row hover:bg-teal-50/50 transition-colors cursor-pointer group" onclick="focusMap({{ $client->latitude ?? 'null' }}, {{ $client->longitude ?? 'null' }}, '{{ $client->name }}')">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full flex items-center justify-center text-teal-600 font-bold shadow-inner">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 client-name">{{ $client->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($client->address, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-600 client-ci">{{ $client->ci }}</td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center space-x-1 bg-slate-100 px-2.5 py-1 rounded-md text-xs font-medium text-slate-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span class="client-phone">{{ $client->phone ?? 'N/A' }}</span>
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
                    <!-- Fila de sin resultados al buscar -->
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="4" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <p class="text-sm font-medium">Sin resultados para tu búsqueda.</p>
                            </div>
                        </td>
                    </tr>
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
                @php
                    $deuda = $client->loans->flatMap->amortizations->where('status', 'pending')->sum('installment_amount');
                    $foto = $client->photo_path ? asset('storage/' . $client->photo_path) : null;
                @endphp
                
                var popupContent = `
                    <div class="text-center w-36">
                        @if($foto)
                            <img src="{{ $foto }}" class="w-16 h-16 object-cover rounded-full mx-auto mb-2 border-2 border-teal-500 shadow-sm">
                        @else
                            <div class="w-16 h-16 bg-teal-100 rounded-full mx-auto mb-2 flex items-center justify-center text-teal-600 font-bold text-xl border-2 border-teal-500 shadow-sm">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                        @endif
                        <strong class="block text-sm text-gray-900 leading-tight">{{ $client->name }}</strong>
                        <span class="block text-xs text-red-500 font-bold mt-1">Deuda: Bs. {{ number_format($deuda, 2) }}</span>
                    </div>
                `;

                var marker = L.marker([{{ $client->latitude }}, {{ $client->longitude }}])
                    .bindPopup(popupContent)
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

    // === BUSCADOR EN TIEMPO REAL ===
    function filterClients() {
        const input = document.getElementById('searchClients');
        const filter = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#clientsBody .client-row');
        const noResults = document.getElementById('noResultsRow');
        const clearBtn = document.getElementById('clearSearchClients');
        const countEl = document.getElementById('searchResultCount');
        let visibleCount = 0;

        // Mostrar/ocultar botón de limpiar
        clearBtn.classList.toggle('hidden', filter === '');
        countEl.classList.toggle('hidden', filter === '');

        rows.forEach(function(row) {
            const name = row.querySelector('.client-name')?.textContent.toLowerCase() || '';
            const ci   = row.querySelector('.client-ci')?.textContent.toLowerCase() || '';
            const phone = row.querySelector('.client-phone')?.textContent.toLowerCase() || '';

            if (name.includes(filter) || ci.includes(filter) || phone.includes(filter)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar fila "Sin resultados"
        noResults.classList.toggle('hidden', visibleCount > 0 || filter === '');

        if (filter !== '') {
            countEl.textContent = visibleCount + ' resultado' + (visibleCount !== 1 ? 's' : '') + ' encontrado' + (visibleCount !== 1 ? 's' : '');
        }
    }

    function clearClientSearch() {
        document.getElementById('searchClients').value = '';
        filterClients();
    }
</script>
@endpush
