@extends('layouts.app')

@section('title', 'Produccion | ' . config('app.name'))
@section('eyebrow', 'Agenda')
@section('heading', 'Produccion')
@section('actions')
    <a class="button button-primary" href="{{ route('production-tasks.create') }}">Nueva actividad</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Pedido</th>
                    <th>Responsable</th>
                    <th>Registro</th>
                    <th>Entrega</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td><a class="font-semibold text-emerald-800" href="{{ route('production-tasks.show', $task) }}">{{ $task->title }}</a></td>
                        <td>{{ $task->order->folio }} - {{ $task->order->client->name }}</td>
                        <td>{{ $task->assignee?->name ?: 'Sin asignar' }}</td>
                        <td>{{ $task->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $task->due_date?->format('d/m/Y') ?: 'Sin fecha' }}</td>
                        <td><span class="badge badge-muted">{{ $statuses[$task->status] ?? $task->status }}</span></td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('production-tasks.toggle-status', $task),
                                'active' => $task->status !== 'cancelled',
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('production-tasks.edit', $task) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">Aun no hay actividades de produccion.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tasks->links() }}</div>
@endsection
