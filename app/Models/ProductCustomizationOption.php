<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductCustomizationOption extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'product_id',
        'material_category_id',
        'category_name',
        'material_id',
        'name',
        'values',
        'price',
        'quantity',
        'allows_quantity',
        'quantity_label',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'allows_quantity' => 'boolean',
            'price' => 'decimal:2',
            'quantity' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function materialCategory()
    {
        return $this->belongsTo(MaterialCategory::class);
    }

    public function valueList(): array
    {
        return collect(preg_split('/\r\n|\r|\n|,/', (string) $this->values))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->values()
            ->all();
    }
}
