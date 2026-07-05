@extends('layouts.app')

@section('title', 'Editar producto | ' . config('app.name'))
@section('eyebrow', 'Productos')
@section('heading', 'Editar producto')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('products.update', $product) }}">
        @method('PUT')
        @include('products._form')
    </form>
@endsection
