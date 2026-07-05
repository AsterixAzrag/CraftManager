@csrf

<div class="form-grid">
    <label class="field">
        <span>Nombre</span>
        <input name="name" value="{{ old('name', $client->name ?? '') }}" required autofocus>
        @error('name') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Telefono</span>
        <input name="phone" value="{{ old('phone', $client->phone ?? '') }}">
        @error('phone') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Correo</span>
        <input type="email" name="email" value="{{ old('email', $client->email ?? '') }}">
        @error('email') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field md:col-span-2">
        <span>Direccion</span>
        <textarea name="address" rows="3">{{ old('address', $client->address ?? '') }}</textarea>
        @error('address') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field md:col-span-2">
        <span>Notas</span>
        <textarea name="notes" rows="3">{{ old('notes', $client->notes ?? '') }}</textarea>
        @error('notes') <small>{{ $message }}</small> @enderror
    </label>
</div>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('clients.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar</button>
</div>
