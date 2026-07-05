<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\BusinessSetting;
use App\Models\InventoryMovement;
use App\Models\Material;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const STATUSES = [
        'registered' => 'Registrado',
        'production' => 'En produccion',
        'ready' => 'Listo',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
    ];

    public function index()
    {
        $orders = Order::query()
            ->with('client')
            ->latest()
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create()
    {
        return view('orders.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateOrder($request);
        $items = $this->validItems($data['items']);
        if ($items === []) {
            return back()->withErrors(['items' => 'Agrega al menos un producto al pedido.'])->withInput();
        }
        if ($stockErrors = $this->stockErrors($items)) {
            return back()->withErrors(['items' => implode(' ', $stockErrors)])->withInput();
        }
        if ($capacityResponse = $this->capacityResponse($request, $data, $items)) {
            return $capacityResponse;
        }
        $subtotal = $this->subtotal($items);
        $discount = (float) ($data['discount'] ?? 0);

        DB::transaction(function () use ($data, $items, $subtotal, $discount) {
            $order = Order::create([
                'client_id' => $data['client_id'],
                'folio' => $this->nextFolio($data['order_date']),
                'status' => $data['status'],
                'order_date' => $data['order_date'],
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => max($subtotal - $discount, 0),
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($items);
            $this->deductInventoryForOrder($order, $items);
        });

        return redirect()
            ->route('orders.index')
            ->with('status', 'Pedido registrado correctamente.');
    }

    public function show(Order $order)
    {
        return view('orders.show', [
            'order' => $order->load(['client', 'items.product']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(Order $order)
    {
        return view('orders.edit', $this->formData($order->load('items')));
    }

    public function update(Request $request, Order $order)
    {
        $data = $this->validateOrder($request);
        $items = $this->validItems($data['items']);
        if ($items === []) {
            return back()->withErrors(['items' => 'Agrega al menos un producto al pedido.'])->withInput();
        }
        if ($stockErrors = $this->stockErrors($items)) {
            return back()->withErrors(['items' => implode(' ', $stockErrors)])->withInput();
        }
        if ($capacityResponse = $this->capacityResponse($request, $data, $items, $order)) {
            return $capacityResponse;
        }
        $subtotal = $this->subtotal($items);
        $discount = (float) ($data['discount'] ?? 0);

        DB::transaction(function () use ($order, $data, $items, $subtotal, $discount) {
            $order->update([
                'client_id' => $data['client_id'],
                'status' => $data['status'],
                'order_date' => $data['order_date'],
                'due_date' => $data['due_date'] ?? null,
                'delivered_at' => $data['status'] === 'delivered' ? now() : null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => max($subtotal - $discount, 0),
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->delete();
            $order->items()->createMany($items);
        });

        return redirect()
            ->route('orders.index')
            ->with('status', 'Pedido actualizado correctamente.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('status', 'Pedido eliminado correctamente.');
    }

    public function toggleStatus(Order $order)
    {
        $order->update([
            'status' => $order->status === 'cancelled' ? 'registered' : 'cancelled',
            'delivered_at' => null,
        ]);

        return redirect()
            ->route('orders.index')
            ->with('status', $order->status === 'cancelled' ? 'Pedido desactivado correctamente.' : 'Pedido activado correctamente.');
    }

    public function cancel(Order $order)
    {
        if (in_array($order->status, ['delivered', 'cancelled'], true)) {
            return back()->with('status', 'El pedido no se puede cancelar desde su estado actual.');
        }

        $order->update([
            'status' => 'cancelled',
            'delivered_at' => null,
        ]);

        return back()->with('status', 'Pedido cancelado correctamente.');
    }

    public function advance(Order $order)
    {
        $nextStatus = [
            'registered' => 'production',
            'production' => 'ready',
            'ready' => 'delivered',
        ][$order->status] ?? null;

        if (! $nextStatus) {
            return back()->with('status', 'El pedido no tiene una siguiente fase disponible.');
        }

        DB::transaction(function () use ($order, $nextStatus) {
            $changedAt = now();
            $phaseName = self::STATUSES[$nextStatus];

            $order->update([
                'status' => $nextStatus,
                'delivered_at' => $nextStatus === 'delivered' ? $changedAt : null,
            ]);

            ProductionTask::create([
                'order_id' => $order->id,
                'assigned_to' => auth()->id(),
                'title' => $order->folio . ' paso a ' . $phaseName,
                'status' => $nextStatus === 'production' ? 'in_progress' : 'done',
                'start_date' => $changedAt->toDateString(),
                'due_date' => $order->due_date?->toDateString() ?? $changedAt->toDateString(),
                'completed_at' => $nextStatus === 'production' ? null : $changedAt,
            ]);
        });

        return back()->with('status', 'Pedido actualizado a ' . self::STATUSES[$nextStatus] . '.');
    }

    private function formData(?Order $order = null): array
    {
        return [
            'order' => $order,
            'clients' => Client::query()->orderBy('name')->get(),
            'products' => Product::query()
                ->with(['customizationOptions.material', 'customizationOptions.materialCategory'])
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'statuses' => self::STATUSES,
        ];
    }

    private function validateOrder(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'status' => ['required', 'in:' . implode(',', array_keys(self::STATUSES))],
            'order_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'confirm_overtime' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function validItems(array $items): array
    {
        return collect($items)
            ->filter(fn (array $item) => filled($item['description'] ?? null) || filled($item['product_id'] ?? null))
            ->map(function (array $item) {
                $quantity = (int) ($item['quantity'] ?? 1);
                $product = filled($item['product_id'] ?? null)
                    ? Product::query()->with(['customizationOptions.material', 'customizationOptions.materialCategory'])->find($item['product_id'])
                    : null;
                $unitPrice = $product
                    ? (float) $product->subtotal
                    : (float) ($item['unit_price'] ?? 0);

                return [
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'] ?: $product?->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $quantity * $unitPrice,
                    'customization_details' => $product ? [
                        'product_subtotal' => (float) $product->subtotal,
                        'materials_total' => (float) $product->materials_total,
                        'expenses_total' => (float) $product->profit_amount,
                        'suggested_price_adjustment' => (float) $product->suggested_price_adjustment,
                        'production_hours' => (float) $product->production_hours,
                        'materials' => $product->customizationOptions
                            ->filter(fn ($option) => $option->material)
                            ->map(fn ($option) => [
                                'material_id' => $option->material_id,
                                'material_name' => $option->material->name,
                                'category' => $option->materialCategory?->name ?: 'Sin categoria',
                                'unit' => $option->material->unit,
                                'quantity_per_product' => (float) $option->quantity,
                                'required_quantity' => (float) $option->quantity * $quantity,
                                'current_stock' => (float) $option->material->current_stock,
                            ])
                            ->values()
                            ->all(),
                    ] : ['selections' => []],
                ];
            })
            ->values()
            ->all();
    }

    private function stockErrors(array $items): array
    {
        return $this->materialRequirements($items)
            ->filter(fn (array $requirement) => $requirement['required'] > $requirement['stock'])
            ->map(fn (array $requirement) => sprintf(
                'No hay suficiente inventario de %s: necesitas %s %s y hay %s %s.',
                $requirement['material_name'],
                number_format($requirement['required'], 2),
                $requirement['unit'],
                number_format($requirement['stock'], 2),
                $requirement['unit'],
            ))
            ->values()
            ->all();
    }

    private function capacityResponse(Request $request, array $data, array $items, ?Order $currentOrder = null)
    {
        $requiredMinutes = $this->requiredProductionMinutes($items);

        if ($requiredMinutes <= 0) {
            return null;
        }

        $productionDate = $data['due_date'] ?? $data['order_date'];
        $capacity = $this->capacityForDate($productionDate, $currentOrder);
        $regularRemaining = max($capacity['regular_minutes'] - $capacity['used_minutes'], 0);
        $totalRemaining = max($capacity['regular_minutes'] + $capacity['overtime_minutes'] - $capacity['used_minutes'], 0);

        if ($requiredMinutes <= $regularRemaining) {
            return null;
        }

        if ($requiredMinutes <= $totalRemaining) {
            if ($request->boolean('confirm_overtime')) {
                return null;
            }

            return back()
                ->withInput()
                ->with('overtime_warning', sprintf(
                    'El pedido requiere %s. Para el dia %s quedan %s de horario regular, pero puede completarse usando horas extra. ¿Deseas guardar el pedido en horas extra?',
                    $this->durationLabel($requiredMinutes),
                    Carbon::parse($productionDate)->format('d/m/Y'),
                    $this->durationLabel($regularRemaining),
                ));
        }

        return back()
            ->withErrors([
                'due_date' => sprintf(
                    'Las horas no son suficientes para el dia %s. El pedido requiere %s y solo quedan %s incluyendo horas extra. Cambia el dia del pedido.',
                    Carbon::parse($productionDate)->format('d/m/Y'),
                    $this->durationLabel($requiredMinutes),
                    $this->durationLabel($totalRemaining),
                ),
            ])
            ->withInput()
            ->with('capacity_error', 'Las horas disponibles no son suficientes. Cambia el dia del pedido.');
    }

    private function requiredProductionMinutes(array $items): int
    {
        return (int) collect($items)->sum(function (array $item) {
            $hours = (float) data_get($item, 'customization_details.production_hours', 0);

            return (int) round($hours * 60) * (int) ($item['quantity'] ?? 1);
        });
    }

    private function capacityForDate(string $date, ?Order $currentOrder = null): array
    {
        $settings = BusinessSetting::query()->first();
        $productionDate = Carbon::parse($date);
        $workingDays = $settings?->working_days ?: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $isWorkingDay = in_array($this->weekDayKey($productionDate), $workingDays, true);
        $regularMinutes = $isWorkingDay
            ? $this->minutesBetween($settings?->work_start_time ?: '09:00', $settings?->work_end_time ?: '18:00')
            : 0;

        return [
            'regular_minutes' => $regularMinutes,
            'overtime_minutes' => (int) round((float) ($settings?->overtime_hours ?? 0) * 60),
            'used_minutes' => $this->usedProductionMinutes($productionDate, $currentOrder),
        ];
    }

    private function usedProductionMinutes(Carbon $date, ?Order $currentOrder = null): int
    {
        return (int) Order::query()
            ->with('items')
            ->whereNotIn('status', ['cancelled', 'delivered'])
            ->where(function ($query) use ($date) {
                $query
                    ->whereDate('due_date', $date->toDateString())
                    ->orWhere(function ($query) use ($date) {
                        $query
                            ->whereNull('due_date')
                            ->whereDate('order_date', $date->toDateString());
                    });
            })
            ->when($currentOrder, fn ($query) => $query->whereKeyNot($currentOrder->id))
            ->get()
            ->sum(fn (Order $order) => $order->items->sum(function ($item) {
                $hours = (float) data_get($item->customization_details, 'production_hours', 0);

                return (int) round($hours * 60) * (int) $item->quantity;
            }));
    }

    private function minutesBetween(string $start, string $end): int
    {
        $start = substr($start, 0, 5);
        $end = substr($end, 0, 5);
        [$startHours, $startMinutes] = array_map('intval', explode(':', $start));
        [$endHours, $endMinutes] = array_map('intval', explode(':', $end));
        $minutes = ($endHours * 60 + $endMinutes) - ($startHours * 60 + $startMinutes);

        if ($minutes < 0) {
            $minutes += 24 * 60;
        }

        return $minutes;
    }

    private function weekDayKey(Carbon $date): string
    {
        return [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ][$date->dayOfWeekIso];
    }

    private function durationLabel(int $minutes): string
    {
        $hours = intdiv(max($minutes, 0), 60);
        $remainingMinutes = max($minutes, 0) % 60;

        return $hours . ' h ' . $remainingMinutes . ' min';
    }

    private function materialRequirements(array $items): Collection
    {
        return collect($items)
            ->filter(fn (array $item) => filled($item['product_id'] ?? null))
            ->flatMap(function (array $item) {
                $product = Product::query()
                    ->with('customizationOptions.material')
                    ->find($item['product_id']);

                if (! $product) {
                    return [];
                }

                return $product->customizationOptions
                    ->filter(fn ($option) => $option->material)
                    ->map(fn ($option) => [
                        'material_id' => $option->material_id,
                        'material_name' => $option->material->name,
                        'unit' => $option->material->unit,
                        'required' => (float) $option->quantity * (int) $item['quantity'],
                        'stock' => (float) $option->material->current_stock,
                    ]);
            })
            ->groupBy('material_id')
            ->map(function ($requirements) {
                $first = $requirements->first();

                return [
                    'material_name' => $first['material_name'],
                    'unit' => $first['unit'],
                    'required' => $requirements->sum('required'),
                    'stock' => $first['stock'],
                ];
            });
    }

    private function deductInventoryForOrder(Order $order, array $items): void
    {
        $usages = $this->materialUsages($items);

        if ($usages->isEmpty()) {
            return;
        }

        $materials = Material::query()
            ->whereIn('id', $usages->pluck('material_id')->unique()->values())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $requirements = $usages
            ->groupBy('material_id')
            ->map(fn (Collection $items) => $items->sum('required'));

        foreach ($requirements as $materialId => $required) {
            $material = $materials->get($materialId);

            if (! $material || (float) $material->current_stock < (float) $required) {
                throw ValidationException::withMessages([
                    'items' => 'No hay suficiente inventario para registrar el pedido.',
                ]);
            }
        }

        foreach ($requirements as $materialId => $required) {
            $material = $materials->get($materialId);
            $material->current_stock = (float) $material->current_stock - (float) $required;
            $material->save();
        }

        foreach ($usages as $usage) {
            $material = $materials->get($usage['material_id']);

            InventoryMovement::create([
                'material_id' => $material->id,
                'user_id' => auth()->id(),
                'type' => 'exit',
                'quantity' => $usage['required'],
                'unit_cost' => $material->unit_cost,
                'reason' => 'Pedido ' . $order->folio . ' - Producto ' . $usage['product_name'],
                'reference_type' => Order::class,
                'reference_id' => $order->id,
            ]);
        }
    }

    private function materialUsages(array $items): Collection
    {
        return collect($items)
            ->filter(fn (array $item) => filled($item['product_id'] ?? null))
            ->flatMap(function (array $item) {
                $product = Product::query()
                    ->with('customizationOptions.material')
                    ->find($item['product_id']);

                if (! $product) {
                    return [];
                }

                return $product->customizationOptions
                    ->filter(fn ($option) => $option->material)
                    ->map(fn ($option) => [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'material_id' => $option->material_id,
                        'required' => (float) $option->quantity * (int) $item['quantity'],
                    ]);
            })
            ->filter(fn (array $usage) => $usage['required'] > 0)
            ->groupBy(fn (array $usage) => $usage['product_id'] . '-' . $usage['material_id'])
            ->map(function (Collection $usages) {
                $first = $usages->first();

                return [
                    'product_id' => $first['product_id'],
                    'product_name' => $first['product_name'],
                    'material_id' => $first['material_id'],
                    'required' => $usages->sum('required'),
                ];
            })
            ->values();
    }

    private function subtotal(array $items): float
    {
        return (float) collect($items)->sum('total');
    }

    private function nextFolio(string $orderDate): string
    {
        $next = (Order::query()->max('id') ?? 0) + 1;
        $hexCounter = strtoupper(str_pad(dechex($next), 4, '0', STR_PAD_LEFT));
        $date = str_replace('-', '', $orderDate);

        return 'PED-' . auth()->user()->company_id . '-' . $date . '-' . $hexCounter;
    }
}
