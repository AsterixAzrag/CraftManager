<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Material;
use App\Models\Order;
use App\Models\ProductionTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->date('start_date')?->startOfDay() ?? now()->startOfMonth();
        $endDate = $request->date('end_date')?->endOfDay() ?? now()->endOfMonth();

        $ordersInPeriod = Order::query()
            ->whereBetween('order_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $salesTotal = (clone $ordersInPeriod)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $pendingOrders = Order::query()
            ->with('client')
            ->whereIn('status', ['registered', 'production'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $ordersByStatus = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $lowStockMaterials = Material::query()
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $materialUsage = InventoryMovement::query()
            ->select('materials.name', 'materials.unit', DB::raw('sum(inventory_movements.quantity) as total_quantity'))
            ->join('materials', 'materials.id', '=', 'inventory_movements.material_id')
            ->where('inventory_movements.type', 'exit')
            ->where('inventory_movements.active', true)
            ->whereBetween('inventory_movements.created_at', [$startDate, $endDate])
            ->groupBy('materials.name', 'materials.unit')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $workload = ProductionTask::query()
            ->select('users.name', DB::raw('count(production_tasks.id) as total_tasks'))
            ->leftJoin('users', 'users.id', '=', 'production_tasks.assigned_to')
            ->whereIn('production_tasks.status', ['pending', 'in_progress'])
            ->groupBy('users.name')
            ->orderByDesc('total_tasks')
            ->get();

        return view('reports.index', [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'salesTotal' => $salesTotal,
            'orderCount' => (clone $ordersInPeriod)->count(),
            'pendingOrders' => $pendingOrders,
            'ordersByStatus' => $ordersByStatus,
            'lowStockMaterials' => $lowStockMaterials,
            'materialUsage' => $materialUsage,
            'workload' => $workload,
        ]);
    }
}
