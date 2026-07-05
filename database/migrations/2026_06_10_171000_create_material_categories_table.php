<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('material_category_id')->nullable()->after('company_id')->constrained('material_categories')->nullOnDelete();
        });

        Schema::table('product_customization_options', function (Blueprint $table) {
            $table->foreignId('material_category_id')->nullable()->after('product_id')->constrained('material_categories')->nullOnDelete();
        });

        $this->migrateMaterialCategories();
    }

    public function down(): void
    {
        Schema::table('product_customization_options', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_category_id');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_category_id');
        });

        Schema::dropIfExists('material_categories');
    }

    private function migrateMaterialCategories(): void
    {
        $categories = collect();

        DB::table('materials')
            ->select('company_id', 'category as name')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->get()
            ->each(fn ($category) => $categories->push($category));

        DB::table('product_customization_options')
            ->select('company_id', 'category_name as name')
            ->whereNotNull('category_name')
            ->where('category_name', '<>', '')
            ->get()
            ->each(fn ($category) => $categories->push($category));

        $categories
            ->map(fn ($category) => [
                'company_id' => $category->company_id,
                'name' => trim($category->name),
            ])
            ->filter(fn ($category) => $category['name'] !== '')
            ->unique(fn ($category) => $category['company_id'] . '|' . mb_strtolower($category['name']))
            ->each(function (array $category) {
                DB::table('material_categories')->insert([
                    'company_id' => $category['company_id'],
                    'name' => $category['name'],
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        DB::table('materials')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->orderBy('id')
            ->each(function ($material) {
                $category = DB::table('material_categories')
                    ->where('company_id', $material->company_id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($material->category))])
                    ->first();

                if ($category) {
                    DB::table('materials')->where('id', $material->id)->update([
                        'material_category_id' => $category->id,
                    ]);
                }
            });

        DB::table('product_customization_options')
            ->whereNotNull('category_name')
            ->where('category_name', '<>', '')
            ->orderBy('id')
            ->each(function ($option) {
                $category = DB::table('material_categories')
                    ->where('company_id', $option->company_id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($option->category_name))])
                    ->first();

                if ($category) {
                    DB::table('product_customization_options')->where('id', $option->id)->update([
                        'material_category_id' => $category->id,
                    ]);
                }
            });
    }
};
