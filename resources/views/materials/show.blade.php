@extends('layouts.app')

@section('title', $material->name . ' | ' . config('app.name'))
@section('eyebrow', 'Material')
@section('heading', $material->name)
@section('actions')
    <a class="button button-muted" href="{{ route('materials.edit', $material) }}">Editar</a>
@endsection

@section('content')
    <dl class="detail-list max-w-3xl">
        <dt>Categoria</dt><dd>{{ $material->materialCategory?->name ?: 'Sin categoria' }}</dd>
        <dt>Existencia</dt><dd>{{ $material->current_stock }} {{ $material->unit }}</dd>
        <dt>Stock minimo</dt><dd>{{ $material->minimum_stock }} {{ $material->unit }}</dd>
        <dt>Stock maximo</dt><dd>{{ $material->maximum_stock ? $material->maximum_stock . ' ' . $material->unit : 'Sin maximo' }}</dd>
        <dt>Movimientos de inventario</dt><dd>{{ $material->allows_inventory_movements ? 'Permitidos' : 'No permitidos' }}</dd>
        <dt>Costo unitario</dt><dd>${{ number_format((float) $material->unit_cost, 2) }}</dd>
        <dt>Estado</dt><dd>{{ $material->active ? 'Activo' : 'Inactivo' }}</dd>
    </dl>
@endsection
