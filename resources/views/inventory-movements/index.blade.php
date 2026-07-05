@extends('layouts.app')

@section('title', 'Movimientos de inventario | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Movimientos de inventario')
@section('actions')
    <a class="button button-primary" href="{{ route('inventory-movements.create') }}">Nuevo movimiento</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Material</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Responsable</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $movement->material->name }}</td>
                        <td><span class="badge badge-muted">{{ $types[$movement->type] ?? $movement->type }}</span></td>
                        <td>{{ $movement->quantity }} {{ $movement->material->unit }}</td>
                        <td>
                            <span class="block">{{ $movement->user?->name ?: 'Sin registrar' }}</span>
                            @if (! $movement->active && $movement->reverser)
                                <span class="mt-1 block text-xs text-zinc-500">Revertido por {{ $movement->reverser->name }}</span>
                            @endif
                        </td>
                        <td>{{ $movement->reason ?: 'Sin motivo' }}</td>
                        <td><span class="badge {{ $movement->active ? 'badge-ok' : 'badge-muted' }}">{{ $movement->active ? 'Vigente' : 'Revertido' }}</span></td>
                        <td class="text-right">
                            @if ($movement->active && in_array($movement->type, ['entry', 'exit'], true))
                                <form class="inline" method="POST" action="{{ route('inventory-movements.reverse', $movement) }}" onsubmit="return confirm('Se va a revertir este movimiento y se ajustara el inventario del material {{ $movement->material->name }}.');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-sm font-medium text-red-700" type="submit">Revertir</button>
                                </form>
                                <span class="mx-2 text-zinc-300">|</span>
                            @elseif ($movement->type === 'adjustment')
                                <span class="text-sm text-zinc-400">No reversible</span>
                                <span class="mx-2 text-zinc-300">|</span>
                            @endif
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('inventory-movements.show', $movement) }}">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-state">Aun no hay movimientos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $movements->links() }}</div>
@endsection
