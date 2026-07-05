<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('production_hours', 8, 2)->default(0)->after('description');
            $table->decimal('suggested_price_adjustment', 12, 2)->default(0)->after('profit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'production_hours',
                'suggested_price_adjustment',
            ]);
        });
    }
};
