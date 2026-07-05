<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables() as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }

        if (DB::table('users')->exists()) {
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'Empresa inicial',
                'email' => DB::table('users')->value('email') ?? 'empresa@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($this->tables() as $tableName) {
                DB::table($tableName)->whereNull('company_id')->update(['company_id' => $companyId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_reverse($this->tables()) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('company_id');
            });
        }
    }

    private function tables(): array
    {
        return [
            'users',
            'business_settings',
            'clients',
            'products',
            'materials',
            'orders',
            'inventory_movements',
            'production_tasks',
        ];
    }
};
