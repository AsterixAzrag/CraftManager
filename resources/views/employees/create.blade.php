@extends('layouts.app')

@section('title', 'Nuevo empleado | ' . config('app.name'))
@section('eyebrow', 'Empleados')
@section('heading', 'Nuevo empleado')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('employees.store') }}">
        @include('employees._form')
    </form>
@endsection
