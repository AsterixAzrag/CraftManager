@extends('layouts.app')

@section('title', 'Editar material | ' . config('app.name'))
@section('eyebrow', 'Materiales')
@section('heading', 'Editar material')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('materials.update', $material) }}">
        @method('PUT')
        @include('materials._form')
    </form>
@endsection
