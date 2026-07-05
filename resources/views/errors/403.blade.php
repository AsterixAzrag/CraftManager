<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sin permiso | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-950">
    <main class="mx-auto flex min-h-screen max-w-lg items-center px-4">
        <section class="w-full border border-zinc-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase text-emerald-700">403</p>
            <h1 class="mt-1 text-2xl font-semibold">No tienes permiso para esta seccion</h1>
            <p class="mt-3 text-sm text-zinc-600">Tu rol actual no permite acceder a este modulo.</p>
            <a class="button button-primary mt-6" href="{{ route('dashboard') }}">Volver al dashboard</a>
        </section>
    </main>
</body>
</html>
