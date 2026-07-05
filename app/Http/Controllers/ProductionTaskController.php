<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductionTask;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionTaskController extends Controller
{
    private const STATUSES = [
        'pending' => 'Pendiente',
        'in_progress' => 'En proceso',
        'done' => 'Terminada',
        'cancelled' => 'Cancelada',
    ];

    public function index()
    {
        $tasks = ProductionTask::query()
            ->with(['order.client', 'assignee'])
            ->latest()
            ->orderBy('status')
            ->orderBy('due_date')
            ->paginate(15);

        return view('production-tasks.index', [
            'tasks' => $tasks,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create()
    {
        return view('production-tasks.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateTask($request);
        $data['completed_at'] = $data['status'] === 'done' ? now() : null;

        ProductionTask::create($data);

        return redirect()
            ->route('production-tasks.index')
            ->with('status', 'Actividad de produccion registrada correctamente.');
    }

    public function show(ProductionTask $productionTask)
    {
        return view('production-tasks.show', [
            'task' => $productionTask->load(['order.client', 'assignee']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(ProductionTask $productionTask)
    {
        return view('production-tasks.edit', $this->formData($productionTask));
    }

    public function update(Request $request, ProductionTask $productionTask)
    {
        $data = $this->validateTask($request);
        $data['completed_at'] = $data['status'] === 'done' ? ($productionTask->completed_at ?? now()) : null;

        $productionTask->update($data);

        return redirect()
            ->route('production-tasks.index')
            ->with('status', 'Actividad de produccion actualizada correctamente.');
    }

    public function destroy(ProductionTask $productionTask)
    {
        $productionTask->delete();

        return redirect()
            ->route('production-tasks.index')
            ->with('status', 'Actividad de produccion eliminada correctamente.');
    }

    public function toggleStatus(ProductionTask $productionTask)
    {
        $isCancelled = $productionTask->status === 'cancelled';

        $productionTask->update([
            'status' => $isCancelled ? 'pending' : 'cancelled',
            'completed_at' => null,
        ]);

        return redirect()
            ->route('production-tasks.index')
            ->with('status', $isCancelled ? 'Actividad activada correctamente.' : 'Actividad desactivada correctamente.');
    }

    private function formData(?ProductionTask $task = null): array
    {
        return [
            'task' => $task,
            'orders' => Order::query()->with('client')->latest()->get(),
            'users' => User::query()
                ->where('company_id', auth()->user()->company_id)
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'statuses' => self::STATUSES,
        ];
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', array_keys(self::STATUSES))],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
    }
}
