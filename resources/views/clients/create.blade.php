@extends('layouts.app')

@section('title', 'Nuevo cliente | ' . config('app.name'))
@section('eyebrow', 'Clientes')
@section('heading', 'Nuevo cliente')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('clients.store') }}">
        @include('clients._form')
    </form>
@endsection
