<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->decimal('marketing_unit_cost_percentage', 8, 2)->default(0)->after('currency');
            $table->decimal('taxes_percentage', 8, 2)->default(0)->after('marketing_unit_cost_percentage');
            $table->decimal('contingency_fund_percentage', 8, 2)->default(0)->after('taxes_percentage');
            $table->decimal('platform_commission_percentage', 8, 2)->default(0)->after('contingency_fund_percentage');
            $table->decimal('payment_gateway_percentage', 8, 2)->default(0)->after('platform_commission_percentage');
        });

        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn([
                'profit_type',
                'profit_fixed_value',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('profit_type')->default('percentage')->after('currency');
            $table->decimal('profit_fixed_value', 12, 2)->default(0)->after('profit_percentage');
        });

        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_unit_cost_percentage',
                'taxes_percentage',
                'contingency_fund_percentage',
                'platform_commission_percentage',
                'payment_gateway_percentage',
            ]);
        });
    }
};
