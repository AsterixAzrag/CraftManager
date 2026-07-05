@extends('layouts.app')

@section('title', 'Editar pedido | ' . config('app.name'))
@section('eyebrow', 'Pedidos')
@section('heading', 'Editar pedido ' . $order->folio)

@section('content')
    <form class="form-panel" method="POST" action="{{ route('orders.update', $order) }}">
        @method('PUT')
        @include('orders._form')
    </form>
@endsection
