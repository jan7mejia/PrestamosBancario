<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

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
        ], [
            'ci.unique' => 'Este Carnet de Identidad (CI) ya está registrado para otro cliente en el sistema.',
            'ci.required' => 'El Carnet de Identidad es obligatorio.',
            'name.required' => 'El nombre completo es obligatorio.'
        ]);

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
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)->with('success', 'Datos del cliente actualizados exitosamente.');
    }
}
