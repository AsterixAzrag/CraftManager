@extends('layouts.app')

@section('title', 'Editar categoria de material | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Editar categoria de material')

@section('content')
    <form method="POST" action="{{ route('material-categories.update', $materialCategory) }}" class="form-card">
        @method('PUT')
        @include('material-categories._form')
    </form>
@endsection
