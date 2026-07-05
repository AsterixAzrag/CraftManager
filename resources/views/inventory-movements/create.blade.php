@extends('layouts.app')

@section('title', 'Nuevo movimiento | ' . config('app.name'))
@section('eyebrow', 'Inventario')
@section('heading', 'Nuevo movimiento')

@section('content')
    <form class="form-panel" method="POST" action="{{ route('inventory-movements.store') }}">
        @csrf
        <div class="form-grid">
            <label class="field">
                <span>Material</span>
                <select name="material_id" required>
                    <option value="">Selecciona un material</option>
                    @foreach ($materials as $material)
                        <option value="{{ $material->id }}" @selected(old('material_id') == $material->id)>
                            {{ $material->name }} - stock actual: {{ $material->current_stock }} {{ $material->unit }}
                        </option>
                    @endforeach
                </select>
                @error('material_id') <small>{{ $message }}</small> @enderror
            </label>

            <label class="field">
                <span>Tipo</span>
                <select name="type" required>
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type') <small>{{ $message }}</small> @enderror
            </label>

            <label class="field">
                <span>Cantidad</span>
                <input type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity', 1) }}" required>
                @error('quantity') <small>{{ $message }}</small> @enderror
            </label>

            <label class="field">
                <span>Costo unitario</span>
                <input type="number" step="0.01" min="0" name="unit_cost" value="{{ old('unit_cost') }}">
                @error('unit_cost') <small>{{ $message }}</small> @enderror
            </label>

            <label class="field md:col-span-2">
                <span>Motivo</span>
                <textarea name="reason" rows="3">{{ old('reason') }}</textarea>
                @error('reason') <small>{{ $message }}</small> @enderror
            </label>
        </div>

        <div class="form-actions">
            <a class="button button-muted" href="{{ route('inventory-movements.index') }}">Cancelar</a>
            <button class="button button-primary" type="submit">Guardar movimiento</button>
        </div>
    </form>
@endsection
