@extends('layouts.app')

@section('title', 'Empleados | ' . config('app.name'))
@section('eyebrow', 'Usuarios')
@section('heading', 'Empleados')
@section('actions')
    <a class="button button-primary" href="{{ route('employees.create') }}">Nuevo empleado</a>
@endsection

@section('content')
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td><a class="font-semibold text-emerald-800" href="{{ route('employees.show', $employee) }}">{{ $employee->name }}</a></td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $roles[$employee->role] ?? $employee->role }}</td>
                        <td><span class="badge {{ $employee->active ? 'badge-ok' : 'badge-muted' }}">{{ $employee->active ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="text-right">
                            @include('shared._toggle_status_button', [
                                'action' => route('employees.toggle-status', $employee),
                                'active' => $employee->active,
                            ])
                            <span class="mx-2 text-zinc-300">|</span>
                            <a class="text-sm font-medium text-zinc-700" href="{{ route('employees.edit', $employee) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">Aun no hay empleados registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $employees->links() }}</div>
@endsection
