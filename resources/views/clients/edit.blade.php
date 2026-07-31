@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Editar Cliente: {{ $client->name }}</h1>
    <p class="text-gray-500">Actualiza los datos personales y ubicación en el mapa.</p>
</div>

<div class="glass rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <form action="{{ route('clients.update', $client) }}" method="POST" enctype="multipart/form-data" class="p-8">
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
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fotografía del Cliente</label>
                    <div id="photo-drop-zone" class="relative flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-2xl bg-slate-50 cursor-pointer hover:border-teal-400 hover:bg-teal-50/40 transition-all duration-300 overflow-hidden" style="min-height: 140px;">
                        @if($client->photo_path)
                            <img id="photo-preview" src="{{ $client->photo_url }}" alt="Foto actual" class="absolute inset-0 w-full h-full object-cover rounded-2xl">
                            <div id="photo-hint" class="hidden flex-col items-center justify-center gap-2 py-6 px-4 z-10 text-center">
                        @else
                            <img id="photo-preview" src="" alt="preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl">
                            <div id="photo-hint" class="flex flex-col items-center justify-center gap-2 py-6 px-4 z-10 text-center">
                        @endif
                                <div class="w-14 h-14 bg-teal-100 rounded-full flex items-center justify-center mb-1">
                                    <svg class="w-7 h-7 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-600">Clic para cambiar la foto</p>
                                <p class="text-xs text-gray-400">JPG, PNG &bull; Máx. 2MB</p>
                            </div>
                        <input type="file" name="photo" id="photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </div>
                    @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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

    // === PREVIEW DE FOTO ===
    document.getElementById('photo').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            const hint = document.getElementById('photo-hint');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            hint.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
