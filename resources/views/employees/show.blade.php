@extends('layouts.app')

@section('title', $employee->name . ' | ' . config('app.name'))
@section('eyebrow', 'Empleado')
@section('heading', $employee->name)
@section('actions')
    <a class="button button-muted" href="{{ route('employees.edit', $employee) }}">Editar</a>
@endsection

@section('content')
    <dl class="detail-list max-w-3xl">
        <dt>Correo</dt><dd>{{ $employee->email }}</dd>
        <dt>Telefono</dt><dd>{{ $employee->phone ?: 'Sin telefono' }}</dd>
        <dt>Rol</dt><dd>{{ $roles[$employee->role] ?? $employee->role }}</dd>
        <dt>Estado</dt><dd>{{ $employee->active ? 'Activo' : 'Inactivo' }}</dd>
    </dl>
@endsection
