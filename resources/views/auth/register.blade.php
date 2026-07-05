<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-950">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-4">
        <form class="w-full border border-zinc-200 bg-white p-6" method="POST" action="{{ route('register.store') }}">
            @csrf
            <p class="text-xs font-semibold uppercase text-emerald-700">CraftManager</p>
            <h1 class="mt-1 text-2xl font-semibold">Crear usuario</h1>

            <div class="mt-6 space-y-4">

                <label class="field">
                    <span>Nombre *</span>
                    <input name="name" value="{{ old('name') }}" required autofocus>
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Correo *</span>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Telefono</span>
                    <input name="phone" value="{{ old('phone') }}">
                    @error('phone') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Nombre de la empresa *</span>
                    <input name="company_name" value="{{ old('company_name') }}" required>
                    @error('company_name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Correo de la empresa (opcional)</span>
                    <input type="email" name="company_email" value="{{ old('company_email') }}">
                    @error('company_email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Contrasena *</span>
                    <input type="password" name="password" required>
                    <p class="mt-1 text-xs text-zinc-500">Debe incluir mayusculas, minusculas, numeros y signos.</p>
                    @error('password') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Confirmar contrasena *</span>
                    <input type="password" name="password_confirmation" required>
                </label>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a class="text-sm font-medium text-emerald-800" href="{{ route('login') }}">Ya tengo cuenta</a>
                <button class="button button-primary" type="submit">Registrarme</button>
            </div>
        </form>
    </main>
</body>
</html>
