<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryMovementController extends Controller
{
    private const TYPES = [
        'entry' => 'Entrada',
        'exit' => 'Salida',
        'adjustment' => 'Ajuste',
    ];

    public function index()
    {
        $movements = InventoryMovement::query()
            ->with(['material', 'user', 'reverser'])
            ->latest()
            ->paginate(15);

        return view('inventory-movements.index', [
            'movements' => $movements,
            'types' => self::TYPES,
        ]);
    }

    public function create()
    {
        return view('inventory-movements.create', [
            'materials' => Material::query()
                ->where('active', true)
                ->where('allows_inventory_movements', true)
                ->orderBy('name')
                ->get(),
            'types' => self::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id' => [
                'required',
                Rule::exists('materials', 'id')
                    ->where('company_id', auth()->user()->company_id)
                    ->where('active', true)
                    ->where('allows_inventory_movements', true),
            ],
            'type' => ['required', 'in:' . implode(',', array_keys(self::TYPES))],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data) {
            $material = Material::query()->lockForUpdate()->findOrFail($data['material_id']);
            abort_unless($material->allows_inventory_movements, 403, 'Este material no admite movimientos de inventario.');
            $quantity = (float) $data['quantity'];

            if ($data['type'] === 'entry') {
                $material->current_stock = (float) $material->current_stock + $quantity;
            }

            if ($data['type'] === 'exit') {
                $material->current_stock = max((float) $material->current_stock - $quantity, 0);
            }

            if ($data['type'] === 'adjustment') {
                $material->current_stock = $quantity;
            }

            if (isset($data['unit_cost'])) {
                $material->unit_cost = $data['unit_cost'];
            }

            $material->save();

            InventoryMovement::create([
                'material_id' => $material->id,
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'quantity' => $quantity,
                'unit_cost' => $data['unit_cost'] ?? null,
                'reason' => $data['reason'] ?? null,
            ]);
        });

        return redirect()
            ->route('inventory-movements.index')
            ->with('status', 'Movimiento de inventario registrado correctamente.');
    }

    public function show(InventoryMovement $inventoryMovement)
    {
        return view('inventory-movements.show', [
            'movement' => $inventoryMovement->load(['material', 'user', 'reverser']),
            'types' => self::TYPES,
        ]);
    }

    public function edit(InventoryMovement $inventoryMovement)
    {
        return redirect()->route('inventory-movements.show', $inventoryMovement);
    }

    public function update(Request $request, InventoryMovement $inventoryMovement)
    {
        return redirect()->route('inventory-movements.show', $inventoryMovement);
    }

    public function destroy(InventoryMovement $inventoryMovement)
    {
        return redirect()
            ->route('inventory-movements.index')
            ->with('status', 'Los movimientos no se eliminan para conservar el historial.');
    }

    public function toggleStatus(InventoryMovement $inventoryMovement)
    {
        return redirect()
            ->route('inventory-movements.index')
            ->with('status', 'Los movimientos de inventario no se activan o desactivan manualmente. Usa Revertir para corregir el stock.');
    }

    public function reverse(InventoryMovement $inventoryMovement)
    {
        if (! $inventoryMovement->active) {
            return redirect()
                ->route('inventory-movements.index')
                ->with('status', 'Este movimiento ya fue revertido.');
        }

        if (! in_array($inventoryMovement->type, ['entry', 'exit'], true)) {
            return redirect()
                ->route('inventory-movements.index')
                ->with('status', 'Los ajustes de inventario no se pueden revertir automaticamente.');
        }

        DB::transaction(function () use ($inventoryMovement) {
            $movement = InventoryMovement::query()->lockForUpdate()->findOrFail($inventoryMovement->id);
            $material = Material::query()->lockForUpdate()->findOrFail($movement->material_id);
            $quantity = (float) $movement->quantity;

            if (! $movement->active) {
                throw ValidationException::withMessages([
                    'inventory_movement' => 'Este movimiento ya fue revertido.',
                ]);
            }

            if ($movement->type === 'entry') {
                if ((float) $material->current_stock < $quantity) {
                    throw ValidationException::withMessages([
                        'inventory_movement' => 'No se puede revertir la entrada porque el inventario actual es menor que la cantidad del movimiento.',
                    ]);
                }

                $material->current_stock = (float) $material->current_stock - $quantity;
            }

            if ($movement->type === 'exit') {
                $material->current_stock = (float) $material->current_stock + $quantity;
            }

            $material->save();
            $movement->update([
                'active' => false,
                'reversed_by' => auth()->id(),
                'reversed_at' => now(),
            ]);
        });

        return redirect()
            ->route('inventory-movements.index')
            ->with('status', 'Movimiento revertido correctamente.');
    }
}
