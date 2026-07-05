@extends('layouts.app')

@section('title', 'Categorias de material | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Categorias de material')
@section('actions')
    <a class="button button-primary" href="{{ route('material-categories.create') }}">Nueva categoria</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Descripcion</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materialCategories as $materialCategory)
                    <tr>
                        <td><a class="font-semibold text-emerald-800" href="{{ route('material-categories.show', $materialCategory) }}">{{ $materialCategory->name }}</a></td>
                        <td>{{ $materialCategory->description ?: 'Sin descripcion' }}</td>
                        <td><span class="badge {{ $materialCategory->active ? 'badge-ok' : 'badge-muted' }}">{{ $materialCategory->active ? 'Activa' : 'Inactiva' }}</span></td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('material-categories.toggle-status', $materialCategory),
                                'active' => $materialCategory->active,
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('material-categories.edit', $materialCategory) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Aun no hay categorias de material.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $materialCategories->links() }}</div>
@endsection
