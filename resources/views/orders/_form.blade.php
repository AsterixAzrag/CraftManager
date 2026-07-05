@csrf
<input type="hidden" name="confirm_overtime" value="0" data-confirm-overtime>

@if (session('capacity_error'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            alert(@json(session('capacity_error')));
        });
    </script>
@endif

@if (session('overtime_warning'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const field = document.querySelector('[data-confirm-overtime]');

            if (confirm(@json(session('overtime_warning')))) {
                field.value = '1';
                field.form.submit();
            }
        });
    </script>
@endif

<div class="form-grid">
    <label class="field">
        <span>Cliente</span>
        <select name="client_id" required>
            <option value="">Selecciona un cliente</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((string) old('client_id', $order->client_id ?? '') === (string) $client->id)>
                    {{ $client->name }}
                </option>
            @endforeach
        </select>
        @error('client_id') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Estado</span>
        <select name="status" required>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $order->status ?? 'registered') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Fecha de pedido</span>
        <input type="date" name="order_date" value="{{ old('order_date', isset($order) ? $order->order_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('order_date') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Fecha de entrega</span>
        <input type="date" name="due_date" value="{{ old('due_date', isset($order) ? $order->due_date?->format('Y-m-d') : '') }}">
        @error('due_date') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field md:col-span-2">
        <span>Notas del pedido</span>
        <textarea name="notes" rows="3">{{ old('notes', $order->notes ?? '') }}</textarea>
        @error('notes') <small>{{ $message }}</small> @enderror
    </label>
</div>

@php
    $oldItems = old('items');
    $existingItems = isset($order) ? $order->items->map(fn ($item) => [
        'product_id' => $item->product_id,
        'description' => $item->description,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
    ])->all() : [];
    $items = $oldItems ?: ($existingItems ?: [[]]);
@endphp

<section class="mt-6" data-order-items-section>
    <div class="flex items-center justify-between gap-3">
        <h2 class="section-title">Detalle del pedido</h2>
        <button class="button button-muted" type="button" data-add-order-item>Agregar producto</button>
    </div>

    <div class="mt-3 space-y-3" data-order-items-list>
        @foreach ($items as $index => $item)
            @include('orders._item_form', ['item' => $item, 'index' => $index, 'isTemplate' => false])
        @endforeach
    </div>

    <template data-order-item-template>
        @include('orders._item_form', ['item' => [], 'index' => '__INDEX__', 'isTemplate' => true])
    </template>

    @error('items') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror

    <div class="mt-4 grid gap-3 border border-zinc-200 bg-zinc-50 px-3 py-3 sm:grid-cols-2 lg:grid-cols-5" data-order-summary>
        <div>
            <span class="text-xs font-semibold uppercase text-zinc-500">Productos</span>
            <strong class="block text-base font-semibold" data-order-products-total>$0.00</strong>
        </div>
        <label class="field">
            <span class="text-xs font-semibold uppercase text-zinc-500">Descuento</span>
            <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', $order->discount ?? 0) }}" data-order-discount>
            @error('discount') <small>{{ $message }}</small> @enderror
        </label>
        <div class="border-t border-zinc-300 pt-2 sm:border-l sm:border-t-0 sm:pl-4 sm:pt-0">
            <span class="text-xs font-semibold uppercase text-emerald-700">Total</span>
            <strong class="block text-xl font-semibold text-emerald-800" data-order-total>$0.00</strong>
        </div>
        <div class="border-t border-zinc-300 pt-2 lg:border-l lg:border-t-0 lg:pl-4 lg:pt-0">
            <span class="text-xs font-semibold uppercase text-zinc-500">Horas requeridas</span>
            <strong class="block text-xl font-semibold text-zinc-900" data-order-hours>0 h 0 min</strong>
        </div>
        <div class="border-t border-zinc-300 pt-2 lg:border-l lg:border-t-0 lg:pl-4 lg:pt-0">
            <span class="text-xs font-semibold uppercase text-zinc-500">Inventario</span>
            <strong class="block text-xl font-semibold" data-order-inventory-status>Suficiente</strong>
        </div>
    </div>
</section>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('orders.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar pedido</button>
</div>
