<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'material_category_id',
        'name',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'allows_inventory_movements',
        'unit_cost',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'maximum_stock' => 'decimal:2',
            'allows_inventory_movements' => 'boolean',
            'unit_cost' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function materialCategory()
    {
        return $this->belongsTo(MaterialCategory::class);
    }

    public function orderItemMaterials()
    {
        return $this->hasMany(OrderItemMaterial::class);
    }

    public function productCustomizationOptions()
    {
        return $this->hasMany(ProductCustomizationOption::class);
    }
}
