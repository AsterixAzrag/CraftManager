@extends('layouts.app')

@section('title', 'Clientes | ' . config('app.name'))
@section('eyebrow', 'Catalogo')
@section('heading', 'Clientes')
@section('actions')
    <a class="button button-primary" href="{{ route('clients.create') }}">Nuevo cliente</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Registro</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    <tr>
                        <td>
                            <a class="font-semibold text-emerald-800" href="{{ route('clients.show', $client) }}">{{ $client->name }}</a>
                        </td>
                        <td>
                            <span class="block">{{ $client->phone ?: 'Sin telefono' }}</span>
                            <span class="text-zinc-500">{{ $client->email ?: 'Sin correo' }}</span>
                        </td>
                        <td>{{ $client->created_at->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $client->active ? 'badge-ok' : 'badge-muted' }}">{{ $client->active ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('clients.toggle-status', $client),
                                'active' => $client->active,
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('clients.edit', $client) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">Aun no hay clientes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $clients->links() }}</div>
@endsection
