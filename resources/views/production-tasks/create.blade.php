@extends('layouts.app')

@section('title', 'Nueva actividad | ' . config('app.name'))
@section('eyebrow', 'Produccion')
@section('heading', 'Nueva actividad')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('production-tasks.store') }}">
        @include('production-tasks._form')
    </form>
@endsection
