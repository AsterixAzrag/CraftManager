@csrf

<div class="form-grid">
    <label class="field">
        <span>Nombre</span>
        <input name="name" value="{{ old('name', $material->name ?? '') }}" required autofocus>
        @error('name') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Categoria</span>
        <input
            type="search"
            name="material_category_name"
            list="material-category-options"
            value="{{ old('material_category_name', isset($material) ? $material->materialCategory?->name : '') }}"
            placeholder="Buscar o agregar categoria"
            autocomplete="off"
        >
        <datalist id="material-category-options">
            @foreach ($materialCategories as $category)
                <option value="{{ $category->name }}"></option>
            @endforeach
        </datalist>
        @error('material_category_name') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Unidad</span>
        <select name="unit" required>
            @foreach (['Unidades', 'Piezas', 'Metros', 'Kilogramos', 'Litros'] as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $material->unit ?? 'Unidades') === $unit)>{{ $unit }}</option>
            @endforeach
        </select>
        @error('unit') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Stock actual</span>
        <input type="number" step="0.01" min="0" name="current_stock" value="{{ old('current_stock', $material->current_stock ?? 0) }}" required>
        @error('current_stock') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Stock minimo</span>
        <input type="number" step="0.01" min="0" name="minimum_stock" value="{{ old('minimum_stock', $material->minimum_stock ?? 0) }}" required>
        @error('minimum_stock') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Stock maximo</span>
        <input type="number" step="0.01" min="0" name="maximum_stock" value="{{ old('maximum_stock', $material->maximum_stock ?? '') }}">
        @error('maximum_stock') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Costo del material</span>
        <input type="number" step="0.01" min="0" name="unit_cost" value="{{ old('unit_cost', $material->unit_cost ?? 0) }}" required>
        @error('unit_cost') <small>{{ $message }}</small> @enderror
    </label>

    <label class="check-field">
        <input type="checkbox" name="allows_inventory_movements" value="1" @checked(old('allows_inventory_movements', $material->allows_inventory_movements ?? true))>
        <span>Admite movimientos de inventario</span>
    </label>
</div>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('materials.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar</button>
</div>
