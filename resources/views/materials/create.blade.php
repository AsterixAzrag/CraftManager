@extends('layouts.app')

@section('title', 'Nuevo material | ' . config('app.name'))
@section('eyebrow', 'Materiales')
@section('heading', 'Nuevo material')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('materials.store') }}">
        @include('materials._form')
    </form>
@endsection
