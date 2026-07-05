@extends('layouts.app')

@section('title', 'Materiales | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Materiales')
@section('actions')
    <a class="button button-primary" href="{{ route('materials.create') }}">Nuevo material</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Categoria</th>
                    <th>Existencia</th>
                    <th>Stock minimo</th>
                    <th>Stock maximo</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materials as $material)
                    <tr>
                        <td><a class="font-semibold text-emerald-800" href="{{ route('materials.show', $material) }}">{{ $material->name }}</a></td>
                        <td>{{ $material->materialCategory?->name ?: 'Sin categoria' }}</td>
                        <td>{{ $material->current_stock }} {{ $material->unit }}</td>
                        <td>{{ $material->minimum_stock }} {{ $material->unit }}</td>
                        <td>{{ $material->maximum_stock ? $material->maximum_stock . ' ' . $material->unit : 'Sin maximo' }}</td>
                        <td>${{ number_format((float) $material->unit_cost, 2) }}</td>
                        <td>
                            @if (! $material->active)
                                <span class="badge badge-muted">Inactivo</span>
                            @elseif ($material->current_stock <= $material->minimum_stock)
                                <span class="badge badge-warn">Stock bajo</span>
                            @else
                                <span class="badge badge-ok">Activo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('materials.toggle-status', $material),
                                'active' => $material->active,
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('materials.edit', $material) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-state">Aun no hay materiales registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $materials->links() }}</div>
@endsection
