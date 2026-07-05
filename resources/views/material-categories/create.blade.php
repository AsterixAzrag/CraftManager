@extends('layouts.app')

@section('title', 'Nueva categoria de material | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Nueva categoria de material')

@section('content')
    <form method="POST" action="{{ route('material-categories.store') }}" class="form-card">
        @include('material-categories._form')
    </form>
@endsection
