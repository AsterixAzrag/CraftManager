<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'production_hours',
        'base_price',
        'marketing_unit_cost_percentage',
        'taxes_percentage',
        'contingency_fund_percentage',
        'platform_commission_percentage',
        'payment_gateway_percentage',
        'utility_percentage',
        'materials_total',
        'profit_amount',
        'suggested_price_adjustment',
        'subtotal',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'production_hours' => 'decimal:2',
            'marketing_unit_cost_percentage' => 'decimal:2',
            'taxes_percentage' => 'decimal:2',
            'contingency_fund_percentage' => 'decimal:2',
            'platform_commission_percentage' => 'decimal:2',
            'payment_gateway_percentage' => 'decimal:2',
            'utility_percentage' => 'decimal:2',
            'materials_total' => 'decimal:2',
            'profit_amount' => 'decimal:2',
            'suggested_price_adjustment' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customizationOptions()
    {
        return $this->hasMany(ProductCustomizationOption::class)->orderBy('material_category_id')->orderBy('sort_order')->orderBy('name');
    }

    public function expensePercentageTotal(): float
    {
        return collect(self::expensePercentageFields())
            ->sum(fn (string $field) => (float) $this->{$field});
    }

    public static function expensePercentageFields(): array
    {
        return [
            'marketing_unit_cost_percentage',
            'taxes_percentage',
            'contingency_fund_percentage',
            'platform_commission_percentage',
            'payment_gateway_percentage',
            'utility_percentage',
        ];
    }
}
