@csrf

<div class="form-grid">
    <label class="field">
        <span>Nombre</span>
        <input name="name" value="{{ old('name', $product->name ?? '') }}" required autofocus>
        @error('name') <small>{{ $message }}</small> @enderror
    </label>

    @php
        $storedProductionMinutes = (int) round((float) ($product->production_hours ?? 0) * 60);
        $productionHours = old('production_time_hours', intdiv($storedProductionMinutes, 60));
        $productionMinutes = old('production_time_minutes', $storedProductionMinutes % 60);
    @endphp

    <div class="grid gap-3 sm:grid-cols-2">
        <label class="field">
            <span>Horas</span>
            <input type="number" min="0" step="1" name="production_time_hours" value="{{ $productionHours }}">
            @error('production_time_hours') <small>{{ $message }}</small> @enderror
        </label>

        <label class="field">
            <span>Minutos</span>
            <input type="number" min="0" max="59" step="1" name="production_time_minutes" value="{{ $productionMinutes }}">
            @error('production_time_minutes') <small>{{ $message }}</small> @enderror
        </label>
    </div>

    <label class="field md:col-span-2">
        <span>Descripcion</span>
        <textarea name="description" rows="2">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <small>{{ $message }}</small> @enderror
    </label>
</div>

@php
    $oldOptions = old('options');
    $existingOptions = isset($product)
        ? $product->customizationOptions->map(fn ($option) => [
            'material_id' => $option->material_id,
            'quantity' => $option->quantity,
        ])->all()
        : [];
    $options = $oldOptions ?: $existingOptions;
@endphp

<section
    class="mt-5"
    data-product-customizations
>
    <div class="flex items-center justify-between gap-3">
        <h2 class="section-title">Materiales personalizables</h2>
        <button class="button button-muted" type="button" data-add-customization>Agregar material</button>
    </div>

    <div class="mt-3 grid gap-4 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div>
            <datalist id="product-material-options">
                @foreach ($materials as $material)
                    <option
                        value="{{ $material->name }} ({{ $material->unit }})"
                        data-id="{{ $material->id }}"
                        data-category="{{ $material->materialCategory?->name ?: 'Sin categoria' }}"
                        data-unit-cost="{{ $material->unit_cost }}"
                        data-current-stock="{{ $material->current_stock }}"
                    ></option>
                @endforeach
            </datalist>

            <div class="table-wrap">
                <table class="compact-table">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Categoria</th>
                    <th>Precio unitario</th>
                    <th>Cantidad</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody data-customization-list>
                @foreach ($options as $index => $option)
                    @php($selectedMaterial = $materials->firstWhere('id', $option['material_id'] ?? null))
                    <tr data-customization-item>
                        <td class="min-w-64">
                            <input
                                type="search"
                                list="product-material-options"
                                value="{{ $selectedMaterial ? $selectedMaterial->name . ' (' . $selectedMaterial->unit . ')' : '' }}"
                                placeholder="Buscar material"
                                autocomplete="off"
                                data-customization-material-search
                            >
                            <input type="hidden" name="options[{{ $index }}][material_id]" value="{{ $option['material_id'] ?? '' }}" data-customization-material>
                            <button class="mt-1 text-xs font-semibold text-red-700" type="button" data-remove-customization>Quitar</button>
                        </td>
                        <td>
                            <input value="{{ $selectedMaterial?->materialCategory?->name ?: 'Sin categoria' }}" data-customization-category disabled>
                        </td>
                        <td>
                            <input type="number" step="0.01" value="{{ $selectedMaterial?->unit_cost ?? 0 }}" data-customization-unit-cost disabled>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" name="options[{{ $index }}][quantity]" value="{{ $option['quantity'] ?? 1 }}" data-customization-quantity>
                        </td>
                        <td>
                            <input type="number" step="0.01" value="{{ number_format((float) ($selectedMaterial?->unit_cost ?? 0) * (float) ($option['quantity'] ?? 1), 2, '.', '') }}" data-customization-value disabled>
                        </td>
                    </tr>
                @endforeach
            </tbody>
                </table>
            </div>

            <div class="{{ count($options) ? 'hidden' : '' }} border border-t-0 border-dashed border-zinc-300 bg-white px-3 py-4 text-center text-sm text-zinc-500" data-customization-empty>
                No hay materiales personalizables.
            </div>
        </div>

        <section>
            <h2 class="section-title">Gastos y utilidades</h2>
            <div class="table-wrap mt-3">
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenseConcepts as $field => $label)
                            <tr>
                                <td class="font-medium text-zinc-800">{{ $label }}</td>
                                <td class="w-28">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="{{ $field }}"
                                        value="{{ old($field, isset($product) ? $product->{$field} : ($defaultExpensePercentages[$field] ?? 0)) }}"
                                        data-product-expense-percentage
                                        required
                                    >
                                    @error($field) <small class="mt-1 block text-sm text-red-700">{{ $message }}</small> @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <template data-customization-template>
        <tr data-customization-item>
            <td class="min-w-64">
                <input
                    type="search"
                    list="product-material-options"
                    placeholder="Buscar material"
                    autocomplete="off"
                    data-customization-material-search
                >
                <input type="hidden" data-name="material_id" data-customization-material>
                <button class="mt-2 text-sm font-medium text-red-700" type="button" data-remove-customization>Quitar</button>
            </td>
            <td><input value="Sin categoria" data-customization-category disabled></td>
            <td><input type="number" step="0.01" value="0" data-customization-unit-cost disabled></td>
            <td><input type="number" step="0.01" min="0" value="1" data-name="quantity" data-customization-quantity></td>
            <td><input type="number" step="0.01" value="0.00" data-customization-value disabled></td>
        </tr>
    </template>

    <div class="mt-4 grid gap-3 border border-zinc-200 bg-zinc-50 px-3 py-3 sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <span class="text-xs font-semibold uppercase text-zinc-500">Materiales</span>
            <strong class="block text-base font-semibold" data-product-materials-total>$0.00</strong>
        </div>
        <div>
            <span class="text-xs font-semibold uppercase text-zinc-500">
                Gastos y utilidad (<span data-product-expense-percentage-total>0.00</span>%)
            </span>
            <strong class="block text-base font-semibold" data-product-profit-total>$0.00</strong>
        </div>
        <label class="field">
            <span class="text-xs font-semibold uppercase text-zinc-500">Valor adicional</span>
            <input
                type="number"
                step="0.01"
                min="0"
                name="suggested_price_adjustment"
                value="{{ old('suggested_price_adjustment', $product->suggested_price_adjustment ?? 0) }}"
                data-suggested-price-adjustment
            >
            @error('suggested_price_adjustment') <small>{{ $message }}</small> @enderror
        </label>
        <div class="border-t border-zinc-300 pt-2 sm:border-l sm:border-t-0 sm:pl-4 sm:pt-0">
            <span class="text-xs font-semibold uppercase text-emerald-700">Precio sugerido de compra</span>
            <strong class="block text-xl font-semibold text-emerald-800" data-product-subtotal>$0.00</strong>
        </div>
        <div class="border-t border-zinc-300 pt-2 lg:border-l lg:border-t-0 lg:pl-4 lg:pt-0">
            <span class="text-xs font-semibold uppercase text-zinc-500">Posibles con inventario</span>
            <strong class="block text-xl font-semibold text-zinc-900" data-product-inventory-capacity>0</strong>
        </div>
    </div>
</section>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('products.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar</button>
</div>
