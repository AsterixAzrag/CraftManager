@extends('layouts.app')

@section('title', $task->title . ' | ' . config('app.name'))
@section('eyebrow', 'Produccion')
@section('heading', $task->title)
@section('actions')
    <a class="button button-muted" href="{{ route('production-tasks.edit', $task) }}">Editar</a>
@endsection

@section('content')
    <dl class="detail-list max-w-3xl">
        <dt>Pedido</dt><dd>{{ $task->order->folio }} - {{ $task->order->client->name }}</dd>
        <dt>Responsable</dt><dd>{{ $task->assignee?->name ?: 'Sin asignar' }}</dd>
        <dt>Estado</dt><dd>{{ $statuses[$task->status] ?? $task->status }}</dd>
        <dt>Registrada</dt><dd>{{ $task->created_at->format('d/m/Y H:i') }}</dd>
        <dt>Inicio</dt><dd>{{ $task->start_date?->format('d/m/Y') ?: 'Sin fecha' }}</dd>
        <dt>Entrega</dt><dd>{{ $task->due_date?->format('d/m/Y') ?: 'Sin fecha' }}</dd>
        <dt>Terminada</dt><dd>{{ $task->completed_at?->format('d/m/Y H:i') ?: 'No terminada' }}</dd>
        <dt>Descripcion</dt><dd>{{ $task->description ?: 'Sin descripcion' }}</dd>
    </dl>
@endsection
