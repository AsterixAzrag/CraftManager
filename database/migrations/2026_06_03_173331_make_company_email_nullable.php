<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE companies ALTER COLUMN email DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('companies')->whereNull('email')->update(['email' => '']);
        DB::statement('ALTER TABLE companies ALTER COLUMN email SET NOT NULL');
    }
};
