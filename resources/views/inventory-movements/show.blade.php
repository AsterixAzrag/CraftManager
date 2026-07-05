@extends('layouts.app')

@section('title', 'Movimiento de inventario | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Movimiento de inventario')

@section('content')
    <dl class="detail-list max-w-3xl">
        <dt>Fecha</dt><dd>{{ $movement->created_at->format('d/m/Y H:i') }}</dd>
        <dt>Material</dt><dd>{{ $movement->material->name }}</dd>
        <dt>Tipo</dt><dd>{{ $types[$movement->type] ?? $movement->type }}</dd>
        <dt>Cantidad</dt><dd>{{ $movement->quantity }} {{ $movement->material->unit }}</dd>
        <dt>Responsable</dt><dd>{{ $movement->user?->name ?: 'Sin registrar' }}</dd>
        <dt>Estado</dt><dd>{{ $movement->active ? 'Vigente' : 'Revertido' }}</dd>
        @if (! $movement->active)
            <dt>Revertido por</dt><dd>{{ $movement->reverser?->name ?: 'Sin registrar' }}</dd>
            <dt>Fecha de reversion</dt><dd>{{ $movement->reversed_at?->format('d/m/Y H:i') ?: 'Sin fecha' }}</dd>
        @endif
        <dt>Costo unitario</dt><dd>{{ $movement->unit_cost ? '$' . number_format((float) $movement->unit_cost, 2) : 'Sin costo' }}</dd>
        <dt>Motivo</dt><dd>{{ $movement->reason ?: 'Sin motivo' }}</dd>
    </dl>
@endsection
