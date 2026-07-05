@extends('layouts.app')

@section('title', $order->folio . ' | ' . config('app.name'))
@section('eyebrow', 'Pedido')
@section('heading', $order->folio)
@section('actions')
    <a class="button button-muted" href="{{ route('orders.edit', $order) }}">Editar</a>
@endsection

@section('content')
    <div class="detail-grid">
        <dl class="detail-list">
            <dt>Cliente</dt><dd>{{ $order->client->name }}</dd>
            <dt>Estado</dt><dd>{{ $statuses[$order->status] ?? $order->status }}</dd>
            <dt>Pedido</dt><dd>{{ $order->order_date->format('d/m/Y') }}</dd>
            <dt>Entrega</dt><dd>{{ $order->due_date?->format('d/m/Y') ?: 'Sin fecha' }}</dd>
            <dt>Notas</dt><dd>{{ $order->notes ?: 'Sin notas' }}</dd>
        </dl>

        <dl class="detail-list">
            <dt>Subtotal</dt><dd>${{ number_format((float) $order->subtotal, 2) }}</dd>
            <dt>Descuento</dt><dd>${{ number_format((float) $order->discount, 2) }}</dd>
            <dt>Total</dt><dd class="text-xl font-semibold">${{ number_format((float) $order->total, 2) }}</dd>
        </dl>
    </div>

    <section class="mt-8">
        <h2 class="section-title">Productos del pedido</h2>
        <div class="table-wrap mt-3">
            <table>
                <thead>
                    <tr>
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <span class="font-medium">{{ $item->description }}</span>
                                @if ($item->customization_details['product_subtotal'] ?? null)
                                    <span class="block text-sm text-zinc-500">
                                        Precio sugerido del producto: ${{ number_format((float) $item->customization_details['product_subtotal'], 2) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                            <td>${{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
