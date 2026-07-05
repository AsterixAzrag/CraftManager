<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('profit_type')->default('percentage')->after('currency');
            $table->decimal('profit_percentage', 8, 2)->default(0)->after('profit_type');
            $table->decimal('profit_fixed_value', 12, 2)->default(0)->after('profit_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn([
                'profit_type',
                'profit_percentage',
                'profit_fixed_value',
            ]);
        });
    }
};
