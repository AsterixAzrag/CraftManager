@extends('layouts.app')

@section('title', 'Productos | ' . config('app.name'))
@section('eyebrow', 'Catalogo')
@section('heading', 'Productos')
@section('actions')
    <a class="button button-primary" href="{{ route('products.create') }}">Nuevo producto</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio sugerido</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <a class="font-semibold text-emerald-800" href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                            <span class="block max-w-xl truncate text-zinc-500">{{ $product->description ?: 'Sin descripcion' }}</span>
                        </td>
                        <td class="font-semibold">${{ number_format((float) $product->subtotal, 2) }}</td>
                        <td><span class="badge {{ $product->active ? 'badge-ok' : 'badge-muted' }}">{{ $product->active ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('products.toggle-status', $product),
                                'active' => $product->active,
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('products.edit', $product) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Aun no hay productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection
