<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
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
            'name' => 'required|string|max:255',
            'ci' => 'required|string|unique:clients,ci|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
        ], [
            'ci.unique' => 'Este Carnet de Identidad (CI) ya está registrado para otro cliente en el sistema.',
            'ci.required' => 'El Carnet de Identidad es obligatorio.',
            'name.required' => 'El nombre completo es obligatorio.'
        ]);

        try {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                if (!$file->isValid()) {
                    return back()->withErrors(['photo' => 'La imagen supera el límite de peso o está dañada. Intenta con una imagen más pequeña.'])->withInput();
                }
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
                $destinationPath = public_path('uploads/clients');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $validated['photo_path'] = 'uploads/clients/' . $filename;
            }
        } catch (\Exception $e) {
            return back()->withErrors(['photo' => 'Ocurrió un error en el servidor al guardar la foto.'])->withInput();
        }

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Cliente registrado exitosamente.');
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
            'name' => 'required|string|max:255',
            'ci' => 'required|string|max:20|unique:clients,ci,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
        ]);

        try {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                if (!$file->isValid()) {
                    return back()->withErrors(['photo' => 'La imagen supera el límite de peso o está dañada. Intenta con una imagen más pequeña.'])->withInput();
                }

                if ($client->photo_path && file_exists(public_path($client->photo_path))) {
                    @unlink(public_path($client->photo_path));
                }
                if ($client->photo_path && str_starts_with($client->photo_path, 'clients/')) {
                    Storage::disk('public')->delete($client->photo_path);
                }

                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
                $destinationPath = public_path('uploads/clients');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $validated['photo_path'] = 'uploads/clients/' . $filename;
            }
        } catch (\Exception $e) {
            return back()->withErrors(['photo' => 'Ocurrió un error en el servidor al guardar la foto.'])->withInput();
        }

        $client->update($validated);

        return redirect()->route('clients.show', $client)->with('success', 'Datos del cliente actualizados exitosamente.');
    }
}
