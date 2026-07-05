@extends('layouts.app')

@section('title', $client->name . ' | ' . config('app.name'))
@section('eyebrow', 'Cliente')
@section('heading', $client->name)
@section('actions')
    <a class="button button-muted" href="{{ route('clients.edit', $client) }}">Editar</a>
@endsection

@section('content')
    <div class="detail-grid">
        <div>
            <h2 class="section-title">Datos de contacto</h2>
            <dl class="detail-list">
                <dt>Telefono</dt><dd>{{ $client->phone ?: 'Sin telefono' }}</dd>
                <dt>Correo</dt><dd>{{ $client->email ?: 'Sin correo' }}</dd>
                <dt>Direccion</dt><dd>{{ $client->address ?: 'Sin direccion' }}</dd>
                <dt>Notas</dt><dd>{{ $client->notes ?: 'Sin notas' }}</dd>
            </dl>
        </div>
        <div>
            <h2 class="section-title">Pedidos</h2>
            <div class="empty-state border border-zinc-200 bg-white">El historial se llenara cuando agreguemos el modulo de pedidos.</div>
        </div>
    </div>
@endsection
