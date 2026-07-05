<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('notes');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
