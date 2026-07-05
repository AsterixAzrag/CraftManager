@extends('layouts.app')

@section('title', $materialCategory->name . ' | ' . config('app.name'))
@section('eyebrow', 'Categoria de material')
@section('heading', $materialCategory->name)
@section('actions')
    <a class="button button-muted" href="{{ route('material-categories.edit', $materialCategory) }}">Editar</a>
@endsection

@section('content')
    <dl class="detail-list max-w-3xl">
        <dt>Descripcion</dt><dd>{{ $materialCategory->description ?: 'Sin descripcion' }}</dd>
        <dt>Estado</dt><dd>{{ $materialCategory->active ? 'Activa' : 'Inactiva' }}</dd>
    </dl>
@endsection
