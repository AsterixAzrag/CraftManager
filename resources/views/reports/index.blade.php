@extends('layouts.app')

@section('title', 'Reportes | ' . config('app.name'))
@section('eyebrow', 'Indicadores')
@section('heading', 'Reportes administrativos')

@section('content')
    <form class="mb-6 border border-zinc-200 bg-white p-4" method="GET" action="{{ route('reports.index') }}">
        <div class="grid gap-4 md:grid-cols-3">
            <label class="field">
                <span>Desde</span>
                <input type="date" name="start_date" value="{{ $startDate }}">
            </label>
            <label class="field">
                <span>Hasta</span>
                <input type="date" name="end_date" value="{{ $endDate }}">
            </label>
            <div class="flex items-end">
                <button class="button button-primary w-full" type="submit">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="metric">
            <span>Ventas del periodo</span>
            <strong>${{ number_format((float) $salesTotal, 2) }}</strong>
        </div>
        <div class="metric">
            <span>Pedidos del periodo</span>
            <strong>{{ $orderCount }}</strong>
        </div>
        <div class="metric">
            <span>Pedidos pendientes</span>
            <strong>{{ $pendingOrders->count() }}</strong>
        </div>
        <div class="metric">
            <span>Materiales en bajo stock</span>
            <strong>{{ $lowStockMaterials->count() }}</strong>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section>
            <h2 class="section-title">Pedidos pendientes</h2>
            <div class="table-wrap mt-3">
                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Entrega</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingOrders as $order)
                            <tr>
                                <td><a class="font-semibold text-emerald-800" href="{{ route('orders.show', $order) }}">{{ $order->folio }}</a></td>
                                <td>{{ $order->client->name }}</td>
                                <td>{{ $order->due_date?->format('d/m/Y') ?: 'Sin fecha' }}</td>
                                <td>${{ number_format((float) $order->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-state">No hay pedidos pendientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="section-title">Pedidos por estado</h2>
            <div class="table-wrap mt-3">
                <table>
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ordersByStatus as $row)
                            <tr>
                                <td>{{ $row->status }}</td>
                                <td>{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No hay pedidos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="section-title">Materiales con stock bajo</h2>
            <div class="table-wrap mt-3">
                <table>
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Existencia</th>
                            <th>Minimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockMaterials as $material)
                            <tr>
                                <td><a class="font-semibold text-emerald-800" href="{{ route('materials.show', $material) }}">{{ $material->name }}</a></td>
                                <td>{{ $material->current_stock }} {{ $material->unit }}</td>
                                <td>{{ $material->minimum_stock }} {{ $material->unit }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">No hay materiales con stock bajo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="section-title">Materiales utilizados</h2>
            <div class="table-wrap mt-3">
                <table>
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materialUsage as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->total_quantity }} {{ $row->unit }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No hay salidas de inventario en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="xl:col-span-2">
            <h2 class="section-title">Carga de trabajo en produccion</h2>
            <div class="table-wrap mt-3">
                <table>
                    <thead>
                        <tr>
                            <th>Responsable</th>
                            <th>Actividades pendientes/en proceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($workload as $row)
                            <tr>
                                <td>{{ $row->name ?: 'Sin asignar' }}</td>
                                <td>{{ $row->total_tasks }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No hay actividades de produccion pendientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
