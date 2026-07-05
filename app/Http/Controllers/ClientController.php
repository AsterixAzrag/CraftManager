<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::query()->latest()->paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        Client::create($this->validateClient($request));

        return redirect()
            ->route('clients.index')
            ->with('status', 'Cliente registrado correctamente.');
    }

    public function show(Client $client)
    {
        return view('clients.show', [
            'client' => $client->load('orders'),
        ]);
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $client->update($this->validateClient($request));

        return redirect()
            ->route('clients.index')
            ->with('status', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('status', 'Cliente eliminado correctamente.');
    }

    public function toggleStatus(Client $client)
    {
        $client->update(['active' => ! $client->active]);

        return redirect()
            ->route('clients.index')
            ->with('status', $client->active ? 'Cliente activado correctamente.' : 'Cliente desactivado correctamente.');
    }

    private function validateClient(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
