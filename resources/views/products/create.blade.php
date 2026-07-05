@extends('layouts.app')

@section('title', 'Nuevo producto | ' . config('app.name'))
@section('eyebrow', 'Productos')
@section('heading', 'Nuevo producto')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('products.store') }}">
        @include('products._form')
    </form>
@endsection
