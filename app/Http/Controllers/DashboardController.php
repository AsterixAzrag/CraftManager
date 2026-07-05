<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $today = now()->startOfDay();
        $selectedDate = $this->selectedDate($request, $today);
        $canViewOrders = auth()->user()->hasRole('admin', 'sales');

        $orders = $canViewOrders
            ? Order::query()
                ->with('client')
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->whereDate('due_date', $selectedDate)
                ->orderBy('due_date')
                ->get()
            : collect();

        $events = $this->agendaEvents($orders);

        return view('dashboard.index', [
            'agendaDate' => $selectedDate,
            'agendaDayLabel' => $this->dayLabels()[$selectedDate->dayOfWeekIso],
            'agendaEvents' => $events,
            'previousDateUrl' => route('dashboard', ['date' => $selectedDate->copy()->subDay()->toDateString()]),
            'todayUrl' => route('dashboard'),
            'nextDateUrl' => route('dashboard', ['date' => $selectedDate->copy()->addDay()->toDateString()]),
            'overdueEvents' => $this->overdueEvents($today, $canViewOrders),
        ]);
    }

    private function selectedDate(Request $request, Carbon $today): Carbon
    {
        try {
            return $request->filled('date')
                ? Carbon::parse($request->query('date'))->startOfDay()
                : $today->copy();
        } catch (\Throwable) {
            return $today->copy();
        }
    }

    private function agendaEvents(Collection $orders): Collection
    {
        return $orders
            ->map(fn (Order $order) => [
                'type' => 'Pedido',
                'moment' => 'Entrega',
                'title' => $order->folio,
                'subtitle' => $order->client?->name ?: 'Cliente sin nombre',
                'detail' => 'Total: $' . number_format((float) $order->total, 2),
                'status' => $this->orderStatuses()[$order->status] ?? $order->status,
                'status_key' => $order->status,
                'route' => route('orders.show', $order),
                'cancel_route' => route('orders.cancel', $order),
                'advance_route' => route('orders.advance', $order),
                'advance_label' => $this->advanceOrderLabels()[$order->status] ?? null,
                'badge' => 'badge-ok',
            ])
            ->sortBy('title')
            ->values();
    }

    private function overdueEvents(Carbon $today, bool $canViewOrders): Collection
    {
        return $canViewOrders
            ? Order::query()
                ->with('client')
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->whereDate('due_date', '<', $today)
                ->orderBy('due_date')
                ->limit(5)
                ->get()
                ->map(fn (Order $order) => [
                    'type' => 'Pedido',
                    'title' => $order->folio,
                    'subtitle' => $order->client?->name ?: 'Cliente sin nombre',
                    'date' => $order->due_date,
                    'route' => route('orders.show', $order),
                ])
            : collect();
    }

    private function dayLabels(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miercoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sabado',
            7 => 'Domingo',
        ];
    }

    private function orderStatuses(): array
    {
        return [
            'registered' => 'Registrado',
            'production' => 'En produccion',
            'ready' => 'Listo',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
        ];
    }

    private function advanceOrderLabels(): array
    {
        return [
            'registered' => 'Producir',
            'production' => 'Terminar',
            'ready' => 'Entregar',
        ];
    }

}
