<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesion | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-950">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-4">
        <form class="w-full border border-zinc-200 bg-white p-6" method="POST" action="{{ route('login.store') }}">
            @csrf
            <p class="text-xs font-semibold uppercase text-emerald-700">CraftManager</p>
            <h1 class="mt-1 text-2xl font-semibold">Iniciar sesion</h1>

            <div class="mt-6 space-y-4">
                <label class="field">
                    <span>Correo</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Contrasena</span>
                    <input type="password" name="password" required>
                    @error('password') <small>{{ $message }}</small> @enderror
                </label>

                <label class="check-field">
                    <input type="checkbox" name="remember" value="1">
                    <span>Recordarme</span>
                </label>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a class="text-sm font-medium text-emerald-800" href="{{ route('register') }}">Crear cuenta</a>
                <button class="button button-primary" type="submit">Entrar</button>
            </div>
        </form>
    </main>
</body>
</html>
