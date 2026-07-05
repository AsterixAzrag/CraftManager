<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_customization_options', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0)->after('values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_customization_options', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
