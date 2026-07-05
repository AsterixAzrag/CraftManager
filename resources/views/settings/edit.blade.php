@extends('layouts.app')

@section('title', 'Configuracion | ' . config('app.name'))
@section('eyebrow', 'Sistema')
@section('heading', 'Configuracion del negocio')

@section('content')
    @php
        $selectedWorkingDays = old('working_days', $settings->working_days ?: []);
        $workStart = old('work_start_time', $settings->work_start_time ? substr($settings->work_start_time, 0, 5) : '09:00');
        $workEnd = old('work_end_time', $settings->work_end_time ? substr($settings->work_end_time, 0, 5) : '18:00');
        $overtimeHours = old('overtime_hours', $settings->overtime_hours ?? 0);
        $overtimeEnd = old('overtime_end_time', $settings->overtime_end_time ? substr($settings->overtime_end_time, 0, 5) : $workEnd);
        $savedExpenseTotal = collect(array_keys($expenseConcepts))->sum(fn ($field) => (float) old($field, $settings->{$field} ?? 0));
    @endphp

    <form class="space-y-5" method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        <section class="form-panel">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <span class="section-title">Perfil del negocio</span>
                    <h2 class="mt-2 text-2xl font-semibold text-zinc-950">
                        {{ old('business_name', $settings->business_name) ?: 'Negocio sin nombre' }}
                    </h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        Mantiene los datos generales, el horario laboral y los porcentajes que alimentan los calculos de productos.
                    </p>
                </div>
                <span class="badge badge-ok self-start">{{ old('currency', $settings->currency) }}</span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="border border-zinc-200 bg-zinc-50 px-3 py-3">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Horario diario</span>
                    <strong class="mt-1 block text-lg font-semibold text-zinc-950" data-work-hours>0 h 0 min</strong>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-3 py-3">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Dias activos</span>
                    <strong class="mt-1 block text-lg font-semibold text-zinc-950">{{ count($selectedWorkingDays) }}</strong>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-3 py-3">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Horas extra</span>
                    <strong class="mt-1 block text-lg font-semibold text-zinc-950" data-overtime-hours>{{ number_format((float) $overtimeHours, 2) }} h</strong>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-3 py-3">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Gastos y utilidad</span>
                    <strong class="mt-1 block text-lg font-semibold text-zinc-950">{{ number_format($savedExpenseTotal, 2) }}%</strong>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="section-title">Datos generales</span>
                    <p class="mt-1 text-sm text-zinc-500">Informacion visible para identificar y administrar la empresa.</p>
                </div>
                <span class="badge badge-muted self-start sm:self-auto">Empresa</span>
            </div>

            <div class="form-grid">
                <label class="field md:col-span-2">
                    <span>Nombre del negocio</span>
                    <input name="business_name" value="{{ old('business_name', $settings->business_name) }}" required>
                    @error('business_name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Telefono</span>
                    <input name="phone" value="{{ old('phone', $settings->phone) }}">
                    @error('phone') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Correo</span>
                    <input type="email" name="email" value="{{ old('email', $settings->email) }}">
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Moneda</span>
                    <input name="currency" maxlength="3" value="{{ old('currency', $settings->currency) }}" required>
                    @error('currency') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field md:col-span-2">
                    <span>Direccion</span>
                    <textarea name="address" rows="4">{{ old('address', $settings->address) }}</textarea>
                    @error('address') <small>{{ $message }}</small> @enderror
                </label>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
            <div class="form-panel">
                <div class="mb-4">
                    <span class="section-title">Horario laboral</span>
                    <p class="mt-1 text-sm text-zinc-500">Define la jornada para estimar carga normal y posible tiempo extra.</p>
                </div>

                <div class="form-grid">
                    <label class="field">
                        <span>Hora de entrada</span>
                        <input type="time" name="work_start_time" value="{{ $workStart }}" data-work-start required>
                        @error('work_start_time') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="field">
                        <span>Hora de salida</span>
                        <input type="time" name="work_end_time" value="{{ $workEnd }}" data-work-end required>
                        @error('work_end_time') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="field md:col-span-2">
                        <span>Hora de cierre con horas extra</span>
                        <input type="time" name="overtime_end_time" value="{{ $overtimeEnd }}" data-overtime-end required>
                        @error('overtime_end_time') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach ($weekDays as $value => $label)
                        <label class="flex items-center justify-between border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium text-zinc-700 transition hover:border-emerald-500 hover:bg-white">
                            <span>{{ $label }}</span>
                            <input class="h-4 w-4" type="checkbox" name="working_days[]" value="{{ $value }}" @checked(in_array($value, $selectedWorkingDays, true))>
                        </label>
                    @endforeach
                </div>
                @error('working_days') <small class="mt-2 block text-sm text-red-700">{{ $message }}</small> @enderror
                @error('working_days.*') <small class="mt-2 block text-sm text-red-700">{{ $message }}</small> @enderror
            </div>

            <div class="form-panel">
                <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <span class="section-title">Gastos y utilidad del producto</span>
                        <p class="mt-1 text-sm text-zinc-500">Porcentajes base que se cargan al crear o editar productos.</p>
                    </div>
                    <span class="badge badge-muted self-start sm:self-auto">Porcentaje</span>
                </div>

                <div class="table-wrap">
                    <table class="compact-table">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="w-40">Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenseConcepts as $field => $label)
                                <tr>
                                    <td class="font-medium text-zinc-800">{{ $label }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="{{ $field }}"
                                                value="{{ old($field, $settings->{$field} ?? 0) }}"
                                                required
                                            >
                                            <span class="text-sm font-semibold text-zinc-500">%</span>
                                        </div>
                                        @error($field) <small class="mt-1 block text-sm text-red-700">{{ $message }}</small> @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="form-panel flex flex-col gap-3 border-emerald-100 bg-emerald-50 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="text-sm font-semibold text-emerald-900">Configuracion lista para guardar</span>
                <p class="mt-1 text-sm text-emerald-800">Revisa los datos y confirma los cambios de la empresa.</p>
            </div>
            <button class="button button-primary" type="submit">Guardar configuracion</button>
        </div>
    </form>
@endsection
