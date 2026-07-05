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
            $table->decimal('materials_total', 12, 2)->default(0)->after('base_price');
            $table->decimal('profit_amount', 12, 2)->default(0)->after('materials_total');
            $table->decimal('subtotal', 12, 2)->default(0)->after('profit_amount');
        });

        DB::table('products')->orderBy('id')->each(function ($product) {
            $materialsTotal = round((float) DB::table('product_customization_options')
                ->where('product_id', $product->id)
                ->sum('price'), 2);
            $costBeforeProfit = (float) $product->base_price + $materialsTotal;
            $settings = DB::table('business_settings')
                ->where('company_id', $product->company_id)
                ->first();
            $profitAmount = $settings?->profit_type === 'fixed'
                ? (float) $settings->profit_fixed_value
                : $costBeforeProfit * ((float) ($settings?->profit_percentage ?? 0) / 100);
            $profitAmount = round($profitAmount, 2);

            DB::table('products')->where('id', $product->id)->update([
                'materials_total' => $materialsTotal,
                'profit_amount' => $profitAmount,
                'subtotal' => round($costBeforeProfit + $profitAmount, 2),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'materials_total',
                'profit_amount',
                'subtotal',
            ]);
        });
    }
};
