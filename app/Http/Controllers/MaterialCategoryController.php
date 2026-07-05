<?php

namespace App\Http\Controllers;

use App\Models\MaterialCategory;
use Illuminate\Http\Request;

class MaterialCategoryController extends Controller
{
    public function index()
    {
        $materialCategories = MaterialCategory::query()->orderBy('name')->paginate(10);

        return view('material-categories.index', compact('materialCategories'));
    }

    public function create()
    {
        return view('material-categories.create');
    }

    public function store(Request $request)
    {
        MaterialCategory::create($this->validateMaterialCategory($request));

        return redirect()
            ->route('material-categories.index')
            ->with('status', 'Categoria de material registrada correctamente.');
    }

    public function show(MaterialCategory $materialCategory)
    {
        return view('material-categories.show', compact('materialCategory'));
    }

    public function edit(MaterialCategory $materialCategory)
    {
        return view('material-categories.edit', compact('materialCategory'));
    }

    public function update(Request $request, MaterialCategory $materialCategory)
    {
        $materialCategory->update($this->validateMaterialCategory($request, $materialCategory));

        return redirect()
            ->route('material-categories.index')
            ->with('status', 'Categoria de material actualizada correctamente.');
    }

    public function destroy(MaterialCategory $materialCategory)
    {
        $materialCategory->delete();

        return redirect()
            ->route('material-categories.index')
            ->with('status', 'Categoria de material eliminada correctamente.');
    }

    public function toggleStatus(MaterialCategory $materialCategory)
    {
        $materialCategory->update(['active' => ! $materialCategory->active]);

        return redirect()
            ->route('material-categories.index')
            ->with('status', $materialCategory->active ? 'Categoria de material activada correctamente.' : 'Categoria de material desactivada correctamente.');
    }

    private function validateMaterialCategory(Request $request, ?MaterialCategory $materialCategory = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]) + [
            'active' => $materialCategory?->active ?? true,
        ];
    }
}
