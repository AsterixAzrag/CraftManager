@extends('layouts.app')

@section('title', 'Editar empleado | ' . config('app.name'))
@section('eyebrow', 'Empleados')
@section('heading', 'Editar empleado')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('employees.update', $employee) }}">
        @method('PUT')
        @include('employees._form')
    </form>
@endsection
