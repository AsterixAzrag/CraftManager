@php
    $fieldPrefix = $isTemplate ? 'items[__INDEX__]' : "items[$index]";
@endphp

<div class="border border-zinc-200 bg-zinc-50 p-4" data-order-item>
    <div class="mb-3 flex items-center justify-between gap-3">
        <p class="text-sm font-semibold text-zinc-700">Producto del pedido</p>
        <button class="text-sm font-medium text-red-700" type="button" data-remove-order-item>Quitar</button>
    </div>
    <div class="grid gap-3 md:grid-cols-6">
        <label class="field md:col-span-2">
            <span>Producto</span>
            <select name="{{ $fieldPrefix }}[product_id]" data-order-product-select>
                <option value="">Producto libre</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        data-base-price="{{ $product->subtotal }}"
                        data-production-hours="{{ $product->production_hours }}"
                        data-production-minutes="{{ (int) round((float) $product->production_hours * 60) }}"
                        @selected(! $isTemplate && (string) ($item['product_id'] ?? '') === (string) $product->id)
                    >
                        {{ $product->name }} - ${{ number_format((float) $product->subtotal, 2) }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="field md:col-span-2">
            <span>Descripcion</span>
            <input name="{{ $fieldPrefix }}[description]" value="{{ $isTemplate ? '' : ($item['description'] ?? '') }}" placeholder="Ej. Taza personalizada azul">
        </label>

        <label class="field">
            <span>Cantidad</span>
            <input type="number" min="1" name="{{ $fieldPrefix }}[quantity]" value="{{ $isTemplate ? 1 : ($item['quantity'] ?? 1) }}" data-order-quantity>
        </label>

        <label class="field">
            <span>Precio sugerido</span>
            <input type="number" step="0.01" min="0" name="{{ $fieldPrefix }}[unit_price]" value="{{ $isTemplate ? 0 : ($item['unit_price'] ?? 0) }}" data-order-unit-price>
        </label>

        <div class="md:col-span-6" data-order-product-materials>
            @foreach ($products as $product)
                @php($isSelectedProduct = ! $isTemplate && (string) ($item['product_id'] ?? '') === (string) $product->id)
                <div class="{{ $isSelectedProduct ? '' : 'hidden' }} border border-zinc-200 bg-white p-3" data-order-product-materials-panel data-product-id="{{ $product->id }}">
                    <p class="text-sm font-semibold text-zinc-800">Materiales necesarios</p>
                    @if ($product->customizationOptions->isNotEmpty())
                        <div class="table-wrap mt-2">
                            <table class="compact-table">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Categoria</th>
                                        <th>Por producto</th>
                                        <th>Para este pedido</th>
                                        <th>Inventario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->customizationOptions as $option)
                                        <tr>
                                            <td>{{ $option->material?->name ?: 'Sin asociar' }}</td>
                                            <td>{{ $option->materialCategory?->name ?: 'Sin categoria' }}</td>
                                            <td>{{ number_format((float) $option->quantity, 2) }} {{ $option->material?->unit }}</td>
                                            <td>
                                                <span data-required-per-product="{{ $option->quantity }}" data-required-total>
                                                    {{ number_format((float) $option->quantity * (float) ($item['quantity'] ?? 1), 2) }}
                                                </span>
                                                {{ $option->material?->unit }}
                                            </td>
                                            <td>
                                                <span data-current-stock="{{ $option->material?->current_stock ?? 0 }}">
                                                    {{ number_format((float) ($option->material?->current_stock ?? 0), 2) }}
                                                </span>
                                                {{ $option->material?->unit }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="mt-2 text-sm text-zinc-500">Este producto no tiene materiales configurados.</p>
                    @endif
                </div>
            @endforeach

            <div class="{{ filled($item['product_id'] ?? null) ? 'hidden' : '' }} border border-dashed border-zinc-300 bg-white px-4 py-4 text-center text-sm text-zinc-500" data-free-product-message>
                Selecciona un producto para ver sus materiales.
            </div>
        </div>
    </div>
</div>
