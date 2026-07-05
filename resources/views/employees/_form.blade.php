@csrf

<div class="form-grid">
    <label class="field">
        <span>Nombre</span>
        <input name="name" value="{{ old('name', $employee->name ?? '') }}" required autofocus>
        @error('name') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Correo</span>
        <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" required>
        @error('email') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Telefono</span>
        <input name="phone" value="{{ old('phone', $employee->phone ?? '') }}">
        @error('phone') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Rol</span>
        <select name="role" required>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $employee->role ?? 'production') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Contrasena</span>
        <input type="password" name="password" @required(!isset($employee))>
        <p class="mt-1 text-xs text-zinc-500">Debe incluir mayusculas, minusculas, numeros y signos.</p>
        @error('password') <small>{{ $message }}</small> @enderror
    </label>

    <label class="field">
        <span>Confirmar contrasena</span>
        <input type="password" name="password_confirmation" @required(!isset($employee))>
    </label>
</div>

<div class="form-actions">
    <a class="button button-muted" href="{{ route('employees.index') }}">Cancelar</a>
    <button class="button button-primary" type="submit">Guardar empleado</button>
</div>
