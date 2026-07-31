<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /**
     * Guarda una foto y devuelve la ruta/URL para guardar en la BD.
     * En producción (Render) usa S3. En local usa la carpeta public.
     */
    private function savePhoto($file): string
    {
        if (!$file->isValid()) {
            throw new \Exception('La imagen está dañada o supera el límite de tamaño.');
        }

        $extension = $file->getClientOriginalExtension();
        $filename  = 'clients/' . time() . '_' . uniqid() . '.' . $extension;

        if (config('filesystems.default') === 's3' || env('FILESYSTEM_DISK') === 's3') {
            Storage::disk('s3')->put($filename, file_get_contents($file), 'public');
            
            // Construir URL pública de Supabase manualmente si no está configurado AWS_URL
            $endpoint = config('filesystems.disks.s3.endpoint'); // ej: https://xxxx.supabase.co/storage/v1/s3
            $bucket = config('filesystems.disks.s3.bucket');
            
            if (str_contains($endpoint, 'supabase.co')) {
                $publicEndpoint = str_replace('/s3', '/object/public/' . $bucket, $endpoint);
                return $publicEndpoint . '/' . $filename;
            }

            return Storage::disk('s3')->url($filename);
        }

        // Si está en local (Laragon), guardar en public/uploads/clients
        $destinationPath = public_path('uploads/clients');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $localName = time() . '_' . uniqid() . '.' . $extension;
        $file->move($destinationPath, $localName);
        return 'uploads/clients/' . $localName;
    }

    /**
     * Elimina la foto anterior del almacenamiento correspondiente.
     */
    private function deletePhoto(?string $photoPath): void
    {
        if (!$photoPath) return;

        // Si es una URL completa (S3), extraer la ruta relativa y borrar de S3
        if (str_starts_with($photoPath, 'http')) {
            try {
                $parsed = parse_url($photoPath);
                $key    = ltrim($parsed['path'], '/');
                // Quitar el nombre del bucket si está en la ruta (path-style)
                $bucket = config('filesystems.disks.s3.bucket');
                if (str_starts_with($key, $bucket . '/')) {
                    $key = substr($key, strlen($bucket) + 1);
                }
                Storage::disk('s3')->delete($key);
            } catch (\Exception $e) {
                // No interrumpir si no se pudo borrar
            }
            return;
        }

        // Si es ruta local
        if (file_exists(public_path($photoPath))) {
            @unlink(public_path($photoPath));
        }
    }

    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'ci'        => 'required|string|unique:clients,ci|max:20',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
        ], [
            'ci.unique'    => 'Este Carnet de Identidad (CI) ya está registrado para otro cliente en el sistema.',
            'ci.required'  => 'El Carnet de Identidad es obligatorio.',
            'name.required'=> 'El nombre completo es obligatorio.'
        ]);

        try {
            if ($request->hasFile('photo')) {
                $validated['photo_path'] = $this->savePhoto($request->file('photo'));
            }
            Client::create($validated);
            return redirect()->route('clients.index')->with('success', 'Cliente registrado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['photo' => 'Error al guardar la foto: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'ci'        => 'required|string|max:20|unique:clients,ci,' . $client->id,
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
        ]);

        try {
            if ($request->hasFile('photo')) {
                $this->deletePhoto($client->photo_path);
                $validated['photo_path'] = $this->savePhoto($request->file('photo'));
            }
            $client->update($validated);
            return redirect()->route('clients.show', $client)->with('success', 'Datos del cliente actualizados exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['photo' => 'Error al guardar la foto: ' . $e->getMessage()])->withInput();
        }
    }
}
