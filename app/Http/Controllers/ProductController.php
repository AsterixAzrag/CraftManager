<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->orderBy('name')->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        DB::transaction(function () use ($data) {
            $product = Product::create($data['product']);
            $product->customizationOptions()->createMany($data['options']);
        });

        return redirect()
            ->route('products.index')
            ->with('status', 'Producto registrado correctamente.');
    }

    public function show(Product $product)
    {
        return view('products.show', [
            'product' => $product->load(['customizationOptions.material', 'customizationOptions.materialCategory']),
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product->load('customizationOptions.materialCategory'),
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product);

        DB::transaction(function () use ($product, $data) {
            $product->update($data['product']);
            $product->customizationOptions()->delete();
            $product->customizationOptions()->createMany($data['options']);
        });

        return redirect()
            ->route('products.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('status', 'Producto eliminado correctamente.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['active' => ! $product->active]);

        return redirect()
            ->route('products.index')
            ->with('status', $product->active ? 'Producto activado correctamente.' : 'Producto desactivado correctamente.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'production_time_hours' => ['nullable', 'integer', 'min:0'],
            'production_time_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
            'suggested_price_adjustment' => ['nullable', 'numeric', 'min:0'],
            'marketing_unit_cost_percentage' => ['required', 'numeric', 'min:0'],
            'taxes_percentage' => ['required', 'numeric', 'min:0'],
            'contingency_fund_percentage' => ['required', 'numeric', 'min:0'],
            'platform_commission_percentage' => ['required', 'numeric', 'min:0'],
            'payment_gateway_percentage' => ['required', 'numeric', 'min:0'],
            'utility_percentage' => ['required', 'numeric', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.material_id' => [
                'nullable',
                Rule::exists('materials', 'id')->where('company_id', auth()->user()->company_id),
            ],
            'options.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $options = collect($data['options'] ?? [])
            ->filter(fn (array $option) => filled($option['material_id'] ?? null))
            ->values()
            ->map(function (array $option, int $index) {
                $material = Material::query()->with('materialCategory')->findOrFail($option['material_id']);
                $quantity = (float) ($option['quantity'] ?? 0);

                return [
                    'category_name' => null,
                    'material_category_id' => $material->material_category_id,
                    'material_id' => $material->id,
                    'name' => $material->name,
                    'values' => null,
                    'price' => round((float) $material->unit_cost * $quantity, 2),
                    'quantity' => $quantity,
                    'allows_quantity' => false,
                    'quantity_label' => null,
                    'notes' => null,
                    'sort_order' => $index,
                ];
            })
            ->all();
        $pricing = $this->calculatePricing($data, $options);

        return [
            'product' => [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'production_hours' => $this->productionHours($data),
                'base_price' => 0,
                'marketing_unit_cost_percentage' => $data['marketing_unit_cost_percentage'],
                'taxes_percentage' => $data['taxes_percentage'],
                'contingency_fund_percentage' => $data['contingency_fund_percentage'],
                'platform_commission_percentage' => $data['platform_commission_percentage'],
                'payment_gateway_percentage' => $data['payment_gateway_percentage'],
                'utility_percentage' => $data['utility_percentage'],
                ...$pricing,
                'active' => $product?->active ?? true,
            ],
            'options' => $options,
        ];
    }

    private function formData(): array
    {
        $settings = BusinessSetting::query()->first();

        return [
            'materials' => Material::query()
                ->with('materialCategory')
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'expenseConcepts' => $this->expenseConcepts(),
            'defaultExpensePercentages' => $this->defaultExpensePercentages($settings),
        ];
    }

    private function calculatePricing(array $data, array $options): array
    {
        $materialsTotal = round((float) collect($options)->sum('price'), 2);
        $expensePercentageTotal = collect(Product::expensePercentageFields())
            ->sum(fn (string $field) => (float) ($data[$field] ?? 0));
        $profitAmount = round($materialsTotal * ($expensePercentageTotal / 100), 2);

        return [
            'materials_total' => $materialsTotal,
            'profit_amount' => $profitAmount,
            'suggested_price_adjustment' => round((float) ($data['suggested_price_adjustment'] ?? 0), 2),
            'subtotal' => round($materialsTotal + $profitAmount + (float) ($data['suggested_price_adjustment'] ?? 0), 2),
        ];
    }

    private function productionHours(array $data): float
    {
        $hours = (int) ($data['production_time_hours'] ?? 0);
        $minutes = (int) ($data['production_time_minutes'] ?? 0);

        return round($hours + ($minutes / 60), 2);
    }

    private function expenseConcepts(): array
    {
        return [
            'marketing_unit_cost_percentage' => 'Costo unitario de marketing',
            'taxes_percentage' => 'Impuestos',
            'contingency_fund_percentage' => 'Fondo imprevisto',
            'platform_commission_percentage' => 'Comision de plataforma',
            'payment_gateway_percentage' => 'Pasarelas de pago',
            'utility_percentage' => 'Utilidad',
        ];
    }

    private function defaultExpensePercentages(?BusinessSetting $settings): array
    {
        return [
            'marketing_unit_cost_percentage' => (float) ($settings?->marketing_unit_cost_percentage ?? 0),
            'taxes_percentage' => (float) ($settings?->taxes_percentage ?? 0),
            'contingency_fund_percentage' => (float) ($settings?->contingency_fund_percentage ?? 0),
            'platform_commission_percentage' => (float) ($settings?->platform_commission_percentage ?? 0),
            'payment_gateway_percentage' => (float) ($settings?->payment_gateway_percentage ?? 0),
            'utility_percentage' => (float) ($settings?->profit_percentage ?? 0),
        ];
    }
}
