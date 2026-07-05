@extends('layouts.app')

@section('title', 'Pedidos | ' . config('app.name'))
@section('eyebrow', 'Operacion')
@section('heading', 'Pedidos')
@section('actions')
    <a class="button button-primary" href="{{ route('orders.create') }}">Nuevo pedido</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Entrega</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><a class="font-semibold text-emerald-800" href="{{ route('orders.show', $order) }}">{{ $order->folio }}</a></td>
                        <td>{{ $order->client->name }}</td>
                        <td>{{ $order->due_date?->format('d/m/Y') ?: 'Sin fecha' }}</td>
                        <td><span class="badge badge-muted">{{ $statuses[$order->status] ?? $order->status }}</span></td>
                        <td>${{ number_format((float) $order->total, 2) }}</td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('orders.toggle-status', $order),
                                'active' => $order->status !== 'cancelled',
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('orders.edit', $order) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Aun no hay pedidos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
