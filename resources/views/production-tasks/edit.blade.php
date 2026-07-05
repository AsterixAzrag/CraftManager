@extends('layouts.app')

@section('title', 'Editar actividad | ' . config('app.name'))
@section('eyebrow', 'Produccion')
@section('heading', 'Editar actividad')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('production-tasks.update', $task) }}">
        @method('PUT')
        @include('production-tasks._form')
    </form>
@endsection
