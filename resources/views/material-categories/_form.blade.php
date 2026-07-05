@csrf

<div class="form-grid">
    <label class="field">
        <span>Nombre</span>
        <input name="name" value="{{ old('name', $materialCategory->name ?? '') }}" required autofocus>
        @error('name') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field md:col-span-2">
        <span>Descripcion</span>
        <textarea name="description" rows="3">{{ old('description', $materialCategory->description ?? '') }}</textarea>
        @error('description') <small>{{ $message }}</small> @enderror
    </label>
</div>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('material-categories.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar categoria</button>
</div>
