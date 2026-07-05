@extends('layouts.app')

@section('title', $product->name . ' | ' . config('app.name'))
@section('eyebrow', 'Producto')
@section('heading', $product->name)
@section('actions')
    <a class="button button-muted" href="{{ route('products.edit', $product) }}">Editar</a>
@endsection

@section('content')
    <dl class="detail-list max-w-3xl">
        <dt>Tiempo de produccion</dt>
        <dd>
            @php($productionMinutes = (int) round((float) $product->production_hours * 60))
            {{ intdiv($productionMinutes, 60) }} h {{ $productionMinutes % 60 }} min
        </dd>
        <dt>Total de materiales</dt><dd>${{ number_format((float) $product->materials_total, 2) }}</dd>
        <dt>Gastos y utilidad</dt><dd>${{ number_format((float) $product->profit_amount, 2) }}</dd>
        <dt>Valor adicional</dt><dd>${{ number_format((float) $product->suggested_price_adjustment, 2) }}</dd>
        <dt>Precio sugerido de compra</dt><dd class="font-semibold text-emerald-800">${{ number_format((float) $product->subtotal, 2) }}</dd>
        <dt>Productos posibles con inventario</dt>
        <dd>
            @php
                $capacities = $product->customizationOptions
                    ->filter(fn ($option) => $option->material && (float) $option->quantity > 0)
                    ->map(fn ($option) => floor((float) $option->material->current_stock / (float) $option->quantity));
            @endphp
            {{ $capacities->isNotEmpty() ? $capacities->min() : 0 }}
        </dd>
        <dt>Estado</dt><dd>{{ $product->active ? 'Activo' : 'Inactivo' }}</dd>
        <dt>Descripcion</dt><dd>{{ $product->description ?: 'Sin descripcion' }}</dd>
    </dl>

    <section class="mt-8">
        <h2 class="section-title">Gastos y utilidades del producto</h2>
        <div class="table-wrap mt-3 max-w-3xl">
            <table>
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ([
                        'marketing_unit_cost_percentage' => 'Costo unitario de marketing',
                        'taxes_percentage' => 'Impuestos',
                        'contingency_fund_percentage' => 'Fondo imprevisto',
                        'platform_commission_percentage' => 'Comision de plataforma',
                        'payment_gateway_percentage' => 'Pasarelas de pago',
                        'utility_percentage' => 'Utilidad',
                    ] as $field => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td>{{ number_format((float) $product->{$field}, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="section-title">Materiales personalizables por categoria</h2>
        <div class="table-wrap mt-3">
            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Categoria</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->customizationOptions as $option)
                        <tr>
                            <td class="font-semibold">{{ $option->material?->name ?: 'Sin asociar' }}</td>
                            <td>{{ $option->materialCategory?->name ?: 'Sin categoria' }}</td>
                            <td>${{ number_format((float) ($option->material?->unit_cost ?? 0), 2) }}</td>
                            <td>{{ number_format((float) $option->quantity, 2) }}</td>
                            <td>${{ number_format((float) $option->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">Este producto no tiene materiales personalizables configurados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
