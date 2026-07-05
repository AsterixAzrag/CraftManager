<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::query()->with('materialCategory')->orderBy('name')->paginate(10);

        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create', $this->formData());
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            Material::create($this->materialData($request));
        });

        return redirect()
            ->route('materials.index')
            ->with('status', 'Material registrado correctamente.');
    }

    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', [
            'material' => $material,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, Material $material)
    {
        DB::transaction(function () use ($request, $material) {
            $material->update($this->materialData($request, $material));
        });

        return redirect()
            ->route('materials.index')
            ->with('status', 'Material actualizado correctamente.');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('status', 'Material eliminado correctamente.');
    }

    public function toggleStatus(Material $material)
    {
        $material->update(['active' => ! $material->active]);

        return redirect()
            ->route('materials.index')
            ->with('status', $material->active ? 'Material activado correctamente.' : 'Material desactivado correctamente.');
    }

    private function validateMaterial(Request $request, ?Material $material = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'material_category_name' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', Rule::in(['Unidades', 'Piezas', 'Metros', 'Kilogramos', 'Litros'])],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'maximum_stock' => ['nullable', 'numeric', 'min:0', 'gte:minimum_stock'],
            'allows_inventory_movements' => ['sometimes', 'boolean'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]) + [
            'active' => $material?->active ?? true,
            'allows_inventory_movements' => false,
        ];
    }

    private function materialData(Request $request, ?Material $material = null): array
    {
        $data = $this->validateMaterial($request, $material);
        $categoryName = trim((string) ($data['material_category_name'] ?? ''));
        unset($data['material_category_name']);

        $data['material_category_id'] = $categoryName === ''
            ? null
            : $this->findOrCreateCategory($categoryName)->id;

        return $data;
    }

    private function findOrCreateCategory(string $name): MaterialCategory
    {
        $category = MaterialCategory::query()
            ->whereRaw('LOWER(name) = LOWER(?)', [$name])
            ->first();

        return $category ?? MaterialCategory::create([
            'name' => $name,
            'active' => true,
        ]);
    }

    private function formData(): array
    {
        return [
            'materialCategories' => MaterialCategory::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ];
    }
}
