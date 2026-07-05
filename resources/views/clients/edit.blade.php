@extends('layouts.app')

@section('title', 'Editar cliente | ' . config('app.name'))
@section('eyebrow', 'Clientes')
@section('heading', 'Editar cliente')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('clients.update', $client) }}">
        @method('PUT')
        @include('clients._form')
    </form>
@endsection
