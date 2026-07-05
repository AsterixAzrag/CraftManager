<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'business_name',
        'phone',
        'email',
        'address',
        'currency',
        'work_start_time',
        'work_end_time',
        'working_days',
        'overtime_hours',
        'overtime_end_time',
        'marketing_unit_cost_percentage',
        'taxes_percentage',
        'contingency_fund_percentage',
        'platform_commission_percentage',
        'payment_gateway_percentage',
        'profit_percentage',
    ];

    protected function casts(): array
    {
        return [
            'marketing_unit_cost_percentage' => 'decimal:2',
            'taxes_percentage' => 'decimal:2',
            'contingency_fund_percentage' => 'decimal:2',
            'platform_commission_percentage' => 'decimal:2',
            'payment_gateway_percentage' => 'decimal:2',
            'profit_percentage' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'working_days' => 'array',
        ];
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
            'profit_percentage',
        ];
    }
}
