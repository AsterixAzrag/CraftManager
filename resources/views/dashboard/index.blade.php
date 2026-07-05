@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))
@section('eyebrow', 'Operacion')
@section('heading', 'Dashboard')

@section('content')
    <section class="grid min-h-[calc(100vh-9rem)] gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="form-panel flex flex-col">
            <div class="flex flex-col gap-4 border-b border-zinc-200 pb-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="section-title">Agenda diaria</span>
                    <h2 class="mt-2 text-3xl font-semibold text-zinc-950">
                        {{ $agendaDayLabel }} {{ $agendaDate->format('d/m/Y') }}
                    </h2>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a class="button button-muted" href="{{ $previousDateUrl }}">Dia anterior</a>
                    <a class="button button-muted" href="{{ $todayUrl }}">Hoy</a>
                    <a class="button button-muted" href="{{ $nextDateUrl }}">Dia siguiente</a>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Total del dia</span>
                    <strong class="mt-1 block text-2xl font-semibold text-zinc-950">{{ $agendaEvents->count() }}</strong>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Pedidos</span>
                    <strong class="mt-1 block text-2xl font-semibold text-zinc-950">{{ $agendaEvents->where('type', 'Pedido')->count() }}</strong>
                </div>
            </div>

            <div class="mt-5 flex-1 space-y-3">
                @forelse ($agendaEvents as $event)
                    <div class="grid gap-4 border border-zinc-200 bg-white px-4 py-4 text-sm transition hover:border-emerald-500 hover:bg-zinc-50 lg:grid-cols-[10rem_minmax(0,1fr)_14rem]">
                        <div>
                            <span class="badge {{ $event['badge'] }}">{{ $event['type'] }}</span>
                            <span class="mt-2 block text-xs font-semibold uppercase text-zinc-500">{{ $event['moment'] }}</span>
                        </div>

                        <div>
                            <a class="block hover:text-emerald-800" href="{{ $event['route'] }}">
                                <strong class="block text-lg font-semibold text-zinc-950">{{ $event['title'] }}</strong>
                                <span class="mt-1 block text-sm text-zinc-500">{{ $event['subtitle'] }}</span>
                                <span class="mt-2 block text-sm text-zinc-700">{{ $event['detail'] }}</span>
                            </a>
                        </div>

                        <div class="flex flex-col items-start gap-2 lg:items-end">
                            <span class="badge badge-muted">{{ $event['status'] }}</span>

                            @if ($event['type'] === 'Pedido')
                                <div class="flex w-full flex-wrap gap-2 lg:justify-end">
                                    <form method="POST" action="{{ $event['cancel_route'] }}" onsubmit="return confirm('Se va a cancelar el pedido {{ $event['title'] }}. Esta accion cambiara su estado a cancelado.');">
                                        @csrf
                                        @method('PATCH')
                                        <button class="button border-red-700 bg-red-700 text-white hover:bg-red-800" type="submit">Cancelar</button>
                                    </form>

                                    @if ($event['advance_label'])
                                        <form method="POST" action="{{ $event['advance_route'] }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="button button-primary" type="submit">{{ $event['advance_label'] }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex min-h-96 flex-1 items-center justify-center border border-dashed border-zinc-300 bg-zinc-50 px-4 py-10 text-center">
                        <div>
                            <span class="section-title">Sin pedidos</span>
                            <p class="mt-2 text-sm text-zinc-500">No hay pedidos programados para este dia.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <aside class="space-y-5">
            <section class="form-panel">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <span class="section-title">Accesos</span>
                        <p class="mt-1 text-sm text-zinc-500">Crear nuevos registros desde la agenda.</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-2">
                    @if (auth()->user()->hasRole('admin', 'sales'))
                        <a class="button button-muted w-full" href="{{ route('orders.create') }}">Nuevo pedido</a>
                    @endif
                    @if (auth()->user()->hasRole('admin', 'production'))
                        <a class="button button-primary w-full" href="{{ route('production-tasks.create') }}">Nueva actividad</a>
                    @endif
                </div>
            </section>

            <section class="form-panel">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <span class="section-title">Vencidos</span>
                        <p class="mt-1 text-sm text-zinc-500">Pendientes con fecha anterior a hoy.</p>
                    </div>
                    <span class="badge badge-warn">{{ $overdueEvents->count() }}</span>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse ($overdueEvents as $event)
                        <a class="block border border-red-100 bg-red-50 px-3 py-2 text-sm transition hover:border-red-300" href="{{ $event['route'] }}">
                            <span class="text-xs font-semibold uppercase text-red-700">{{ $event['type'] }}</span>
                            <strong class="mt-1 block text-zinc-900">{{ $event['title'] }}</strong>
                            <span class="mt-1 block text-xs text-zinc-500">{{ $event['subtitle'] }} | {{ $event['date']->format('d/m/Y') }}</span>
                        </a>
                    @empty
                        <div class="empty-state border border-zinc-200 bg-zinc-50">No hay pendientes vencidos.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </section>
@endsection
