@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="mb-8 flex justify-between items-center bg-white/60 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-white/40">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Registrar Nuevo Cliente</h1>
        <p class="text-sm text-gray-500 mt-1">Ingresa los datos personales y marca la ubicación en el mapa.</p>
    </div>
</div>

<div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 overflow-hidden">
    <form action="{{ route('clients.store') }}" method="POST" class="p-8 sm:p-10">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Datos Básicos -->
            <div class="space-y-7">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">Nombre Completo</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" 
                           class="block w-full bg-white border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                    @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="ci" class="block text-sm font-bold text-gray-700 mb-1.5">Carnet de Identidad</label>
                        <input type="text" name="ci" id="ci" required value="{{ old('ci') }}" 
                               class="block w-full bg-white border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                        @error('ci') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 mb-1.5">Teléfono/Celular</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                               class="block w-full bg-white border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-bold text-gray-700 mb-1.5">Dirección</label>
                    <textarea name="address" id="address" rows="3" 
                              class="block w-full bg-white border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Selección de Ubicación en Mapa -->
            <div class="flex flex-col space-y-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Ubicación en el Mapa</label>
                    <p class="text-xs text-gray-500 mt-1">Haz clic en el mapa para marcar con precisión la ubicación del cliente.</p>
                </div>
                <div class="p-1.5 bg-gray-100 rounded-2xl border border-gray-200 shadow-inner flex-grow">
                    <div id="map" class="w-full h-64 md:h-full min-h-[300px] rounded-xl z-0"></div>
                </div>
                
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end items-center space-x-4">
            <a href="{{ route('clients.index') }}" class="py-2.5 px-6 border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white py-2.5 px-8 rounded-xl shadow-lg shadow-emerald-500/30 text-sm font-bold transition-all duration-300 transform hover:-translate-y-0.5">
                Guardar Cliente
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([-17.3895, -66.1568], 13); // Cochabamba
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker;

        // Si ya hay coordenadas (por validación fallida)
        var oldLat = document.getElementById('latitude').value;
        var oldLng = document.getElementById('longitude').value;
        if(oldLat && oldLng) {
            marker = L.marker([oldLat, oldLng]).addTo(map);
            map.setView([oldLat, oldLng], 15);
        }

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });
    });
</script>
@endpush
