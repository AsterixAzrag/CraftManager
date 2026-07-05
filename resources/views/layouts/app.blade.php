<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-950">
    @php($businessName = \App\Models\BusinessSetting::query()->value('business_name') ?: 'proyecto-pedidos')
    <div class="flex min-h-screen">
        <aside class="hidden w-64 border-r border-zinc-200 bg-white px-5 py-6 lg:block">
            <a href="{{ route('dashboard') }}" class="block">
                <span class="text-sm font-semibold uppercase text-emerald-700">CraftManager</span>
                <span class="mt-1 block text-xl font-semibold">{{ $businessName }}</span>
            </a>

            <nav class="mt-8 space-y-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                @if (auth()->user()->hasRole('admin', 'sales'))
                    <a class="nav-link {{ request()->routeIs('clients.*') ? 'nav-link-active' : '' }}" href="{{ route('clients.index') }}">Clientes</a>
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'nav-link-active' : '' }}" href="{{ route('products.index') }}">Productos</a>
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'nav-link-active' : '' }}" href="{{ route('orders.index') }}">Pedidos</a>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'nav-link-active' : '' }}" href="{{ route('reports.index') }}">Reportes</a>
                @endif

                @if (auth()->user()->hasRole('admin', 'production'))
                    <a class="nav-link {{ request()->routeIs('material-categories.*') ? 'nav-link-active' : '' }}" href="{{ route('material-categories.index') }}">Categorias de material</a>
                    <a class="nav-link {{ request()->routeIs('materials.*') ? 'nav-link-active' : '' }}" href="{{ route('materials.index') }}">Materiales</a>
                    <a class="nav-link {{ request()->routeIs('inventory-movements.*') ? 'nav-link-active' : '' }}" href="{{ route('inventory-movements.index') }}">Inventario</a>
                    <a class="nav-link {{ request()->routeIs('production-tasks.*') ? 'nav-link-active' : '' }}" href="{{ route('production-tasks.index') }}">Produccion</a>
                @endif

                @if (auth()->user()->isAdmin())
                    <a class="nav-link {{ request()->routeIs('employees.*') ? 'nav-link-active' : '' }}" href="{{ route('employees.index') }}">Empleados</a>
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'nav-link-active' : '' }}" href="{{ route('settings.edit') }}">Configuracion</a>
                @endif
            </nav>
        </aside>

        <main class="flex-1">
            <header class="border-b border-zinc-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase text-emerald-700">@yield('eyebrow', 'Panel')</p>
                        <h1 class="mt-1 text-2xl font-semibold">@yield('heading', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="hidden text-sm text-zinc-600 md:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button button-muted" type="submit">Salir</button>
                        </form>
                        @yield('actions')
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-5 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold">Revisa los campos marcados.</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
