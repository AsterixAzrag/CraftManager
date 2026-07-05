<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('marketing_unit_cost_percentage', 8, 2)->default(0)->after('base_price');
            $table->decimal('taxes_percentage', 8, 2)->default(0)->after('marketing_unit_cost_percentage');
            $table->decimal('contingency_fund_percentage', 8, 2)->default(0)->after('taxes_percentage');
            $table->decimal('platform_commission_percentage', 8, 2)->default(0)->after('contingency_fund_percentage');
            $table->decimal('payment_gateway_percentage', 8, 2)->default(0)->after('platform_commission_percentage');
            $table->decimal('utility_percentage', 8, 2)->default(0)->after('payment_gateway_percentage');
        });

        DB::table('products')->orderBy('id')->each(function ($product) {
            $settings = DB::table('business_settings')
                ->where('company_id', $product->company_id)
                ->first();

            if (! $settings) {
                return;
            }

            $percentages = [
                'marketing_unit_cost_percentage' => (float) $settings->marketing_unit_cost_percentage,
                'taxes_percentage' => (float) $settings->taxes_percentage,
                'contingency_fund_percentage' => (float) $settings->contingency_fund_percentage,
                'platform_commission_percentage' => (float) $settings->platform_commission_percentage,
                'payment_gateway_percentage' => (float) $settings->payment_gateway_percentage,
                'utility_percentage' => (float) $settings->profit_percentage,
            ];
            $materialsTotal = round((float) DB::table('product_customization_options')
                ->where('product_id', $product->id)
                ->sum('price'), 2);
            $expenseAmount = round($materialsTotal * (array_sum($percentages) / 100), 2);

            DB::table('products')->where('id', $product->id)->update([
                ...$percentages,
                'base_price' => 0,
                'materials_total' => $materialsTotal,
                'profit_amount' => $expenseAmount,
                'subtotal' => round($materialsTotal + $expenseAmount, 2),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_unit_cost_percentage',
                'taxes_percentage',
                'contingency_fund_percentage',
                'platform_commission_percentage',
                'payment_gateway_percentage',
                'utility_percentage',
            ]);
        });
    }
};
