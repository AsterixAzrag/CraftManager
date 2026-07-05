@csrf

<div class="form-grid">
    <label class="field">
        <span>Pedido</span>
        <select name="order_id" required>
            <option value="">Selecciona un pedido</option>
            @foreach ($orders as $order)
                <option value="{{ $order->id }}" @selected((string) old('order_id', $task->order_id ?? '') === (string) $order->id)>
                    {{ $order->folio }} - {{ $order->client->name }}
                </option>
            @endforeach
        </select>
        @error('order_id') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Responsable</span>
        <select name="assigned_to">
            <option value="">Sin asignar</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('assigned_to', $task->assigned_to ?? '') === (string) $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        @error('assigned_to') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Titulo</span>
        <input name="title" value="{{ old('title', $task->title ?? '') }}" required>
        @error('title') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Estado</span>
        <select name="status" required>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $task->status ?? 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Inicio</span>
        <input type="date" name="start_date" value="{{ old('start_date', isset($task) ? $task->start_date?->format('Y-m-d') : '') }}">
        @error('start_date') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Entrega</span>
        <input type="date" name="due_date" value="{{ old('due_date', isset($task) ? $task->due_date?->format('Y-m-d') : '') }}">
        @error('due_date') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field md:col-span-2">
        <span>Descripcion</span>
        <textarea name="description" rows="4">{{ old('description', $task->description ?? '') }}</textarea>
        @error('description') <small>{{ $message }}</small> @enderror
    </label>
</div>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('production-tasks.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar actividad</button>
</div>
