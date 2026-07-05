<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_customization_options', function (Blueprint $table) {
            $table->decimal('quantity', 12, 2)->default(1)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('product_customization_options', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
