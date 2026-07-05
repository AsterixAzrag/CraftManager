@extends('layouts.app')

@section('title', 'Nuevo pedido | ' . config('app.name'))
@section('eyebrow', 'Pedidos')
@section('heading', 'Nuevo pedido')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('orders.store') }}">
        @include('orders._form')
    </form>
@endsection
