@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Editar Cliente: {{ $client->name }}</h1>
    <p class="text-gray-500">Actualiza los datos personales y ubicación en el mapa.</p>
</div>

<div class="glass rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <form action="{{ route('clients.update', $client) }}" method="POST" class="p-8">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Datos Básicos -->
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $client->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="ci" class="block text-sm font-medium text-gray-700">Carnet de Identidad</label>
                        <input type="text" name="ci" id="ci" required value="{{ old('ci', $client->ci) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('ci') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono/Celular</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('address', $client->address) }}</textarea>
                </div>
            </div>

            <!-- Selección de Ubicación en Mapa -->
            <div class="flex flex-col space-y-2">
                <label class="block text-sm font-medium text-gray-700">Ubicación en el Mapa</label>
                <p class="text-xs text-gray-500">Haz clic en el mapa para actualizar la ubicación del cliente.</p>
                <div id="map" class="w-full h-64 rounded-md border border-gray-300 z-0"></div>
                
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $client->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $client->longitude) }}">
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ route('clients.show', $client) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-3">
                Cancelar
            </a>
            <button type="submit" class="bg-primary border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Actualizar Cliente
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var startLat = {{ $client->latitude ?? '-17.3895' }};
        var startLng = {{ $client->longitude ?? '-66.1568' }};
        var zoom = {{ $client->latitude ? '15' : '13' }};
        
        var map = L.map('map').setView([startLat, startLng], zoom);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker;

        if({{ $client->latitude ? 'true' : 'false' }}) {
            marker = L.marker([startLat, startLng]).addTo(map);
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
